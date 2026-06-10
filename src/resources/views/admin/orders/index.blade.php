@extends('admin.layouts.app')
@section('title', 'Đơn hàng')
@section('page-title', 'Đơn hàng')
@section('content')
    <div class="x_panel">
        <div class="x_title"><h2>Đơn hàng</h2></div>
        <div class="x_content">
            <form method="GET" class="orders-filter">
                <input type="hidden" name="period" value="custom">
                <div class="orders-filter-top">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Mã đơn, tên, SĐT..." class="form-control">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        @foreach($statuses as $key => $label)<option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="orders-filter-bottom">
                    <div class="orders-periods">
                        @foreach ($periods as $key => $label)
                            <a href="{{ route('admin.orders.index', array_merge(request()->only('q', 'status'), ['period' => $key])) }}" class="btn btn-sm {{ $period === $key ? 'btn-primary' : 'btn-secondary' }}">{{ $label }}</a>
                        @endforeach
                    </div>
                    <div class="orders-filter-dates">
                        <input type="date" name="from" value="{{ $fromInput }}" class="form-control" max="{{ now()->format('Y-m-d') }}">
                        <span class="text-slate-400">~</span>
                        <input type="date" name="to" value="{{ $toInput }}" class="form-control" max="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="orders-filter-actions">
                        <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">Đặt lại</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Mã</th><th>Sản phẩm</th><th>Khách</th><th>Tổng</th><th>Trạng thái</th><th>Ngày</th></tr></thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-start gap-1 admin-link hover:underline">
                                        <span>{{ $order->order_code }}</span>
                                        @if($order->isNew())
                                            <span class="inline-flex h-5 w-5 -translate-y-1 items-center justify-center rounded-full bg-red-500 text-[7px] font-bold leading-none text-white" title="Đơn hàng mới">New</span>
                                        @endif
                                    </a>
                                </td>
                                <td>
                                    @foreach($order->items as $item)
                                        <div class="whitespace-nowrap text-sm text-slate-700">
                                            {{ $item->product_name }}
                                            <span class="text-xs text-slate-400">× {{ $item->quantity }}</span>
                                        </div>
                                    @endforeach
                                    @if($order->items->isEmpty())<span class="text-slate-400">—</span>@endif
                                </td>
                                <td class="whitespace-nowrap">{{ $order->customer_name }}<br><span class="text-xs text-slate-500">{{ $order->customer_phone }}</span></td>
                                <td>{{ $order->formattedTotal() }}</td>
                                <td><span class="badge {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span></td>
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
