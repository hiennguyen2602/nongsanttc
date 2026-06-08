<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            [
                'label' => 'Tổng đơn hàng',
                'value' => '0',
                'change' => '0%',
                'trend' => 'up',
                'icon' => 'orders',
            ],
            [
                'label' => 'Doanh thu',
                'value' => '0đ',
                'change' => '0%',
                'trend' => 'up',
                'icon' => 'revenue',
            ],
            [
                'label' => 'Sản phẩm',
                'value' => '0',
                'change' => '0 mới',
                'trend' => 'neutral',
                'icon' => 'products',
            ],
            [
                'label' => 'Khách hàng',
                'value' => '0',
                'change' => '0 mới',
                'trend' => 'up',
                'icon' => 'users',
            ],
        ];

        $recentOrders = [];
        $recentActivities = [
            ['message' => 'Chào mừng đến với hệ thống quản trị Nông Sản TTC.', 'time' => 'Vừa xong'],
        ];

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentActivities'));
    }
}
