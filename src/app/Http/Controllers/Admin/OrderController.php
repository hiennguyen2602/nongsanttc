<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ResolvesDateRange;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    use ResolvesDateRange;

    public function index(Request $request): View
    {
        $period = $request->input('period', 'all');
        [$fromDate, $toDate] = $this->resolveDateRange($period, $request);

        $orders = Order::query()
            ->with('items.product')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->q, fn ($q) => $q->where(function ($builder) use ($request) {
                $builder->where('order_code', 'like', '%' . $request->q . '%')
                    ->orWhere('customer_name', 'like', '%' . $request->q . '%')
                    ->orWhere('customer_phone', 'like', '%' . $request->q . '%');
            }))
            ->when($fromDate, fn ($q) => $q->where('created_at', '>=', $fromDate))
            ->when($toDate, fn ($q) => $q->where('created_at', '<=', $toDate))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => Order::statusLabels(),
            'periods' => $this->datePeriods(),
            'period' => $period,
            'fromInput' => $fromDate?->format('Y-m-d'),
            'toInput' => $toDate?->format('Y-m-d'),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load('items');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(Order::statusLabels()))],
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }
}
