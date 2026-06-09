<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'orders' => Order::query()->count(),
                'pending_orders' => Order::query()->where('status', Order::STATUS_PENDING)->count(),
                'products' => Product::query()->count(),
                'users' => User::query()->count(),
            ],
            'recentOrders' => Order::query()->latest()->limit(8)->get(),
        ]);
    }
}
