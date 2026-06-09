@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Trang chủ / Dashboard')

@section('content')
    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="admin-stat">
            <p class="admin-stat-label">Tổng đơn hàng</p>
            <p class="admin-stat-value">{{ $stats['orders'] }}</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-label">Chờ xử lý</p>
            <p class="admin-stat-value text-amber-600">{{ $stats['pending_orders'] }}</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-label">Sản phẩm</p>
            <p class="admin-stat-value">{{ $stats['products'] }}</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-label">Người dùng</p>
            <p class="admin-stat-value">{{ $stats['users'] }}</p>
        </div>
    </div>

    <div class="x_panel">
        <div class="x_title">
            <h2>Đơn hàng gần đây</h2>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">Xem tất cả</a>
        </div>
        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Số tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-gent-accent hover:underline">{{ $order->order_code }}</a>
                                </td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ $order->formattedTotal() }}</td>
                                <td><span class="badge badge-secondary">{{ $order->statusLabel() }}</span></td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Chưa có đơn hàng.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
