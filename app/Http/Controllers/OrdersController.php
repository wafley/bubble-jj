<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Order;
use App\Models\UploadService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class OrdersController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        return spaRender($request, 'orders.index');
    }

    public function data(Request $request)
    {
        $orders = Order::with('user.profile', 'service');

        if ($request->status) {
            $orders->where('status', $request->status);
        }

        $orders->orderByRaw("FIELD(status, 'pending') DESC")->orderBy('created_at', 'desc');

        return DataTables::of($orders)
            ->addIndexColumn()
            ->editColumn('orderer', function ($order) {
                $username = $order->user->profile->username_1 ?? '-';
                $phone = $order->user->phone ?? '-';
                return "<strong>{$username}</strong><br><small class='text-primary'>{$phone}</small>";
            })
            ->addColumn('service', fn($order) => $order->service->name)
            ->editColumn('status', function ($row) {
                return "<span class='badge text-bg-{$row->status_color}'>{$row->status_label}</span>";
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y H:i') . '<br><small>' . $row->created_at->diffForHumans() . '</small>';
            })
            ->addColumn('action', function ($row) {
                $detailUrl = route('orders.show', $row->id);
                return "<a href='{$detailUrl}' class='btn btn-sm btn-primary spa-link'>Detail <i class='bi bi-arrow-right'></i></a>";
            })
            ->rawColumns(['orderer', 'status', 'created_at', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $serviceSlug = $request->get('service', '');
        $service = UploadService::where('slug', $serviceSlug)
            ->where('slug', '!=', 'free')
            ->firstOrFail();

        $data = [
            'service' => $service,
        ];

        return spaRender($request, 'orders.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $serviceSlug = $request->get('service', '');
        $service = UploadService::where('slug', $serviceSlug)
            ->where('slug', '!=', 'free')
            ->firstOrFail();

        $rules = [
            'display_type' => 'nullable|in:10,20,30,99',
            'notes' => 'nullable|string',
        ];

        if ($service->type === 'image') {
            $rules['files'] = 'required|array|min:1|max:5';
            $rules['files.*'] = 'image|max:153600'; // 150MB
        } elseif ($service->type === 'video') {
            $rules['file'] = 'required|file|mimetypes:video/*|max:153600'; // 150MB
        }

        $validated = $request->validate($rules);

        return $this->orderService->handle($validated, $service);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $order = Order::with('user.profile', 'service')->findOrFail($id);
        $view = Auth::user()->role->name !== 'user' ? 'orders.show' : 'orders.detail';

        $data = [
            'order' => $order,
        ];

        return spaRender($request, $view, $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order  = Order::with('user.profile', 'service')->findOrFail($id);
        $action = $request->get('action', '');

        $rules = match ($action) {
            'result' => [
                'file_result'   => 'required|file|mimetypes:video/*|max:5120',
                'proof_payment' => 'required|image|max:5120',
            ],
            'reject' => [
                'reject_reason' => 'required|string|max:1000',
            ],
            default => abort(404),
        };

        $validated = $request->validate($rules);

        $result = $this->orderService->process($order, $action, $validated);

        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::with('files')->findOrFail($id);

        if ($order->user_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Tidak punya akses'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['status' => 'error', 'message' => 'Order hanya bisa dihapus jika pending'], 422);
        }

        $result = $this->orderService->remove($id, 'order');
        return response()->json($result);
    }

    public function destroyFile(string $id)
    {
        $file = File::with('order')->findOrFail($id);
        $order = $file->order;

        if ($order->user_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Tidak punya akses'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['status' => 'error', 'message' => 'File hanya bisa dihapus jika order pending'], 422);
        }

        $result = $this->orderService->remove($id, 'file');
        return response()->json($result);
    }
}
