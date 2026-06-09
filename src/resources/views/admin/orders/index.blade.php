@extends('admin.layouts.app')
@section('title', 'Đơn hàng')
@section('page-title', 'Đơn hàng')
@section('content')
    <div class="x_panel">
        <div class="x_title"><h2>Đơn hàng</h2></div>
        <div class="x_content">
            <form method="GET" class="admin-toolbar">
                <div class="admin-search">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Mã đơn, tên, SĐT..." class="form-control">
                    <select name="status" class="form-select" style="width:auto;min-width:10rem">
                        <option value="">Tất cả trạng thái</option>
                        @foreach($statuses as $key => $label)<option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>@endforeach
                    </select>
                    <button type="submit" class="btn btn-secondary btn-sm">Lọc</button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Mã</th><th>Khách</th><th>Tổng</th><th>Trạng thái</th><th>Ngày</th></tr></thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td><a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-gent-accent hover:underline">{{ $order->order_code }}</a></td>
                                <td>{{ $order->customer_name }}<br><span class="text-xs text-slate-500">{{ $order->customer_phone }}</span></td>
                                <td>{{ $order->formattedTotal() }}</td>
                                <td><span class="badge badge-secondary">{{ $order->statusLabel() }}</span></td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $orders->links() }}</div>
        </div>
    </div>
@endsection
