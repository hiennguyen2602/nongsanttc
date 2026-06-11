<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ResolvesDateRange;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use ResolvesDateRange;

    public function index(Request $request): View
    {
        $period = $this->resolveFilterPeriod($request);
        [$fromDate, $toDate] = $this->resolveDateRange($period, $request);

        $query = Order::query();

        if ($fromDate) {
            $query->where('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->where('created_at', '<=', $toDate);
        }

        $statusCounts = (clone $query)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $statusLabels = Order::statusLabels();

        return view('admin.dashboard', [
            'totalOrders' => (int) $statusCounts->sum(),
            'statusLabels' => $statusLabels,
            'statusCounts' => $statusCounts,
            'statusColors' => Order::statusColors(),
            'recentOrders' => (clone $query)->latest()->limit(8)->get(),
            'periods' => $this->datePeriods(),
            'period' => $period,
            'fromInput' => $fromDate?->format('Y-m-d'),
            'toInput' => $toDate?->format('Y-m-d'),
        ]);
    }
}
