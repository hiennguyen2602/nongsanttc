<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->q, fn ($q) => $q->where(function ($builder) use ($request) {
                $builder->where('order_code', 'like', '%' . $request->q . '%')
                    ->orWhere('customer_name', 'like', '%' . $request->q . '%')
                    ->orWhere('customer_phone', 'like', '%' . $request->q . '%');
            }))
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => Order::statusLabels(),
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
