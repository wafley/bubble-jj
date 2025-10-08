<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DataJJ;
use App\Models\Order;
use App\Models\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $services = UploadService::rememberCache('services_all', 3600, fn() => UploadService::all());
        $orders = Order::with('service')->where('user_id', Auth::id())->latest('updated_at')->get();
        $videos = DataJJ::where('user_id', Auth::id())->where('sts_active', true)->get()->groupBy('display_type');

        $data = [
            'services' => $services,
            'orders' => $orders,
            'videos' => $videos
        ];

        return spaRender($request, 'user.home', $data);
    }
}
