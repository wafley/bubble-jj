<?php

namespace App\Services;

use getID3;
use App\Models\File;
use App\Models\Order;
use App\Models\DataJJ;
use App\Models\UploadService;
use App\Helpers\ActivityLogger;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderService
{
    public function handle(array $data, UploadService $service): array
    {
        $fileInputKey = $service->type === 'image' ? 'files' : 'file';
        return $this->createFiles($data, $service, $fileInputKey);
    }

    public function process(Order $order, string $action, array $data): array
    {
        return DB::transaction(function () use ($order, $action, $data) {

            switch ($action) {
                case 'result':
                    $videoFile = $data['file_result'];
                    $proofFile = $data['proof_payment'];

                    $getID3   = new \getID3();
                    $fileInfo = $getID3->analyze($videoFile->getPathname());
                    $duration = $fileInfo['playtime_seconds'] ?? 0;

                    if ($duration > 60) {
                        return [
                            'status'  => 'error',
                            'message' => 'Durasi video melebihi 60 detik.',
                        ];
                    }

                    $order->files()->each(function ($file) {
                        $this->removeFile($file);
                    });

                    $existing = DataJJ::where('user_id', $order->user_id)
                        ->where('display_type', $order->display_type ?? 10)
                        ->first();

                    if ($existing && Storage::disk('public')->exists('jj/' . $existing->filename)) {
                        Storage::disk('public')->delete('jj/' . $existing->filename);
                    }

                    $videoPath = $videoFile->store('jj', 'public');
                    $proofPath = $proofFile->store('bukti_trf', 'public');

                    $order->update([
                        'proof_payment' => basename($proofPath),
                        'status'        => 'approved',
                    ]);

                    DataJJ::updateOrCreate(
                        [
                            'user_id'      => $order->user_id,
                            'display_type' => $order->display_type ?? 10,
                        ],
                        [
                            'username_1'   => $order->user->profile->username_1,
                            'username_2'   => $order->user->profile->username_2,
                            'filename'     => basename($videoPath),
                            'duration'     => round($duration),
                            'size'         => $videoFile->getSize(),
                            'sts_active'   => true,
                        ]
                    );

                    ActivityLogger::logFor(
                        $order,
                        "Order {$order->id} disetujui.",
                        Auth::user(),
                        [
                            'filename' => basename($videoPath),
                            'duration' => $duration,
                            'size'     => $videoFile->getSize(),
                        ],
                        'order_approved',
                    );

                    return [
                        'status'        => 'success',
                        'message'       => 'Video dan bukti transfer berhasil diupload!',
                        'redirect'      => route('orders.index'),
                        'redirect_type' => 'spa',
                    ];
                    break;
                case 'reject':
                    $reason = $data['reject_reason'];

                    $order->files()->each(function ($file) {
                        $this->removeFile($file);
                    });

                    $order->update([
                        'status'        => 'rejected',
                        'reject_reason' => $reason,
                    ]);

                    ActivityLogger::logFor(
                        $order,
                        "Order {$order->id} ditolak.",
                        Auth::user(),
                        ['reject_reason' => $reason],
                        'order_rejected',
                    );

                    return [
                        'status'        => 'success',
                        'message'       => 'Pesanan berhasil ditolak dan file terkait telah dihapus.',
                        'redirect'      => route('orders.index'),
                        'redirect_type' => 'spa',
                    ];
                    break;
                default:
                    throw new \InvalidArgumentException("Unknown action: $action");
            }
        });
    }

    public function remove(string $id, string $type = 'order'): array
    {
        return DB::transaction(function () use ($id, $type) {
            switch ($type) {
                case 'order':
                    $order = Order::with('files')->findOrFail($id);

                    $order->files()->each(function ($file) {
                        $this->removeFile($file);
                    });

                    $order->delete();

                    ActivityLogger::logFor(
                        $order,
                        "Order {$order->id} dibatalkan.",
                        Auth::user(),
                        [],
                        'order_cancelled',
                    );

                    return [
                        'status' => 'success',
                        'message' => 'Pesanan berhasil dibatalkan.',
                        'redirect'      => route('home'),
                        'redirect_type' => 'spa',
                    ];
                    break;
                case 'file':
                    $file = File::with('order')->findOrFail($id);
                    $order = $file->order;

                    if ($order->files()->count() <= 1) {
                        return [
                            'status' => 'error',
                            'message' => 'Tidak bisa menghapus file terakhir. Harus ada minimal satu file per pesanan.'
                        ];
                    }

                    $this->removeFile($file);

                    ActivityLogger::logFor(
                        $file,
                        "File {$file->id} Order {$order->id} dihapus.",
                        Auth::user(),
                        [],
                        'order_file_deleted',
                    );

                    return [
                        'status' => 'success',
                        'message' => 'File berhasil dihapus.',
                        'redirect' => route('orders.update', $order->id),
                        'redirect_type' => 'spa',
                    ];
                    break;
                default:
                    throw new \InvalidArgumentException("Unknown type: $type");
            }
        });
    }

    private function createFiles(array $data, UploadService $service, string $fileKey): array
    {
        return DB::transaction(function () use ($data, $service, $fileKey) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'service_id' => $service->id,
                'display_type' => $data['display_type'] ?? 10,
                'notes' => $data['notes'] ?? null,
            ]);

            ActivityLogger::logFor(
                $order,
                "Order {$order->id} berhasil dibuat.",
                $order,
                [
                    'service' => $service->slug,
                    'display_type' => $data['display_type'] ?? 10,
                    'notes' => $data['notes'] ?? null,
                ],
                'order_created',
            );

            $files = is_array($data[$fileKey]) ? $data[$fileKey] : [$data[$fileKey]];

            foreach ($files as $file) {
                $this->storeFile($order, $file, $service->type);
            }

            return [
                'status' => 'success',
                'message' => "Order {$service->name} berhasil dibuat, tunggu admin memproses.",
            ];
        });
    }

    private function storeFile(Order $order, UploadedFile $file, string $type): File
    {
        $directory = "orders/{$type}";

        $path = $file->store($directory, 'public');
        $filename = basename($path);

        $duration = null;

        if ($type === 'video') {
            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($file->getPathname());
            $duration = isset($fileInfo['playtime_seconds'])
                ? (float) $fileInfo['playtime_seconds']
                : 0;
        }

        $storedFile = File::create([
            'order_id' => $order->id,
            'filename' => $filename,
            'type' => $type,
            'duration' => $duration,
            'size' => $file->getSize(),
        ]);

        return $storedFile;
    }

    protected function removeFile(File $file): void
    {
        $path = "orders/{$file->type}/{$file->filename}";
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        $file->delete();
    }
}
