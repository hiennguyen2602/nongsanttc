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
        $period = $request->input('period', 'recent');
        [$fromDate, $toDate] = $this->resolveDateRange($period, $request);

        $ranged = function () use ($fromDate, $toDate) {
            $query = Order::query();

            if ($fromDate) {
                $query->where('created_at', '>=', $fromDate);
            }

            if ($toDate) {
                $query->where('created_at', '<=', $toDate);
            }

            return $query;
        };

        $statusCounts = $ranged()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $statusLabels = Order::statusLabels();
        unset($statusLabels[Order::STATUS_CANCELLED]);

        return view('admin.dashboard', [
            'totalOrders' => $ranged()->count(),
            'statusLabels' => $statusLabels,
            'statusCounts' => $statusCounts,
            'statusColors' => Order::statusColors(),
            'cancelledCount' => $statusCounts[Order::STATUS_CANCELLED] ?? 0,
            'recentOrders' => $ranged()->latest()->limit(8)->get(),
            'periods' => $this->datePeriods(),
            'period' => $period,
            'fromInput' => $fromDate?->format('Y-m-d'),
            'toInput' => $toDate?->format('Y-m-d'),
        ]);
    }
}
