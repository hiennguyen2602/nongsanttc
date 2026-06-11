@extends('admin.layouts.app')

@section('title', $customer->name)
@section('page-title', 'Chi tiết khách hàng')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Khách hàng', 'url' => route('admin.customers.index')],
        ['label' => 'Chi tiết khách hàng'],
    ])])
@endsection

@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>{{ $customer->name }}</h2>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
        </div>
        <div class="x_content">
            <dl class="product-detail-list mb-8">
                <div><dt>Số điện thoại</dt><dd>{{ $customer->phone }}</dd></div>
                <div><dt>Email</dt><dd>{{ $customer->email ?? '—' }}</dd></div>
                <div><dt>Địa chỉ</dt><dd>{{ $customer->address ?? '—' }}</dd></div>
                <div><dt>Tổng đơn hàng</dt><dd>{{ $customer->orders->count() }}</dd></div>
            </dl>

            <h3 class="panel-title mb-3">Lịch sử đơn hàng</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="cell-code">Mã</th>
                            <th class="cell-price">Tổng</th>
                            <th class="cell-status">Trạng thái</th>
                            <th class="cell-date">Ngày</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->orders as $order)
                            <tr>
                                <td class="cell-code"><a href="{{ route('admin.orders.show', $order) }}" class="font-medium admin-link hover:underline">{{ $order->order_code }}</a></td>
                                <td class="cell-price">{{ $order->formattedTotal() }}</td>
                                <td class="cell-status">@include('admin.partials.status-badge', ['label' => $order->statusLabel(), 'class' => $order->statusBadgeClass()])</td>
                                <td class="cell-date">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-slate-500">Chưa có đơn hàng.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
