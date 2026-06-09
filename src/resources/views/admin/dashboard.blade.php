@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Trang chủ / Dashboard')

@section('content')
    <div class="x_panel">
        <div class="x_content">
            <div class="dashboard-filter">
                <div class="dashboard-periods">
                    @foreach ($periods as $key => $label)
                        <a href="{{ route('admin.dashboard', ['period' => $key]) }}" class="btn btn-sm {{ $period === $key ? 'btn-primary' : 'btn-secondary' }}">{{ $label }}</a>
                    @endforeach
                </div>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="dashboard-custom">
                    <input type="hidden" name="period" value="custom">
                    <div class="dashboard-dates">
                        <input type="date" name="from" value="{{ $fromInput }}" class="form-control" max="{{ now()->format('Y-m-d') }}">
                        <span class="text-slate-400">~</span>
                        <input type="date" name="to" value="{{ $toInput }}" class="form-control" max="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="dashboard-actions">
                        <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">Đặt lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mb-4 grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <div class="admin-stat">
            <p class="admin-stat-label">Tổng đơn</p>
            <p class="admin-stat-value">{{ $totalOrders }}</p>
        </div>
        @foreach ($statusLabels as $key => $label)
            <div class="admin-stat">
                <p class="admin-stat-label">{{ $label }}</p>
                <p class="admin-stat-value" style="color:{{ $statusColors[$key] ?? '' }}">{{ $statusCounts[$key] ?? 0 }}</p>
            </div>
        @endforeach
        <div class="admin-stat">
            <p class="admin-stat-label">Đã hủy</p>
            <p class="admin-stat-value" style="color:{{ $statusColors[\App\Models\Order::STATUS_CANCELLED] }}">{{ $cancelledCount }}</p>
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
                                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-start gap-1 font-medium text-[#015096] hover:underline">
                                        <span>{{ $order->order_code }}</span>
                                        @if($order->isNew())
                                            <span class="inline-flex h-5 w-5 -translate-y-1 items-center justify-center rounded-full bg-red-500 text-[7px] font-bold leading-none text-white" title="Đơn hàng mới">New</span>
                                        @endif
                                    </a>
                                </td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ $order->formattedTotal() }}</td>
                                <td><span class="badge {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span></td>
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
