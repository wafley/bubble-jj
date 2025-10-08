<?php

namespace App\Services;

use getID3;
use App\Models\DataJJ;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class JejeService
{
    public function upload(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $file = $data['file'];

            $path = $file->store('jj', 'public');
            $filename = basename($path);

            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($file->getPathname());
            $duration = isset($fileInfo['playtime_seconds'])
                ? (float) $fileInfo['playtime_seconds']
                : 0;

            $jj = DataJJ::updateOrCreate([
                'user_id' => Auth::id(),
                'display_type' => $data['display_type'] ?? 10,
            ], [
                'username_1' => Auth::user()->profile->username_1,
                'username_2' => Auth::user()->profile->username_2,
                'filename' => $filename,
                'duration' => $duration,
                'size' => $file->getSize(),
                'sts_active' => true,
            ]);

            ActivityLogger::logFor(
                $jj,
                "Video berhasil diupload ke Data JJ.",
                Auth::user(),
                [
                    'filename' => $filename,
                    'duration' => $duration,
                    'size' => $file->getSize(),
                ],
                'upload_jj',
            );

            return [
                'status' => 'success',
                'message' => "Video berhasil diupload ke Data JJ.",
            ];
        });
    }
}
