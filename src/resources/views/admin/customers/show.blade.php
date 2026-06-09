@extends('admin.layouts.app')

@section('title', $customer->name)
@section('page-title', 'Chi tiết khách hàng')

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
                            <th>Mã</th>
                            <th>Tổng</th>
                            <th>Trạng thái</th>
                            <th>Ngày</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->orders as $order)
                            <tr>
                                <td><a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-[#015096] hover:underline">{{ $order->order_code }}</a></td>
                                <td>{{ $order->formattedTotal() }}</td>
                                <td><span class="badge {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span></td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
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
