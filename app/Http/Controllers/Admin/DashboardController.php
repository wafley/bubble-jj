<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(7)->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->toDateString());

        $logs = Activity::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $logsByDate = Activity::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $logsByType = Activity::selectRaw('log_name, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('log_name')
            ->orderByDesc('total')
            ->get();

        $data = [
            'logs' => $logs,
            'logsByDate' => $logsByDate,
            'logsByType' => $logsByType,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        return spaRender($request, 'admin.dashboard', $data);
    }
}
