@extends('admin.layouts.app')
@section('title', $order->order_code)
@section('page-title', 'Đơn ' . $order->order_code)
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Đơn hàng', 'url' => route('admin.orders.index')],
        ['label' => 'Chi tiết đơn hàng'],
    ])])
@endsection
@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-4">
            <div class="x_panel">
                <div class="x_title"><h2>Sản phẩm</h2></div>
                <div class="x_content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead><tr><th class="cell-text-wide">Tên</th><th class="cell-num">SL</th><th class="cell-price">Đơn giá</th><th class="cell-price text-end">Thành tiền</th></tr></thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="cell-text-wide">
                                            @if($item->product)
                                                <a href="{{ route('admin.products.show', $item->product) }}" class="hover:underline">{{ $item->product_name }}</a>
                                            @else
                                                {{ $item->product_name }}
                                            @endif
                                            @if($item->variant_label)<span class="text-xs text-red-600">({{ $item->variant_label }})</span>@endif
                                        </td>
                                        <td class="cell-num">{{ $item->quantity }}</td>
                                        <td class="cell-price">{{ format_money($item->unit_price) }}</td>
                                        <td class="cell-price text-end">{{ format_money($item->line_total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="space-y-4">
            <div class="x_panel">
                <div class="x_title"><h2>Khách hàng</h2></div>
                <div class="x_content text-sm">
                    <dl class="space-y-2">
                        <div class="flex gap-2">
                            <dt class="w-15 shrink-0 font-semibold text-slate-500">Tên</dt>
                            <dd class="flex-1">
                                @if($order->customer)
                                    <a href="{{ route('admin.customers.show', $order->customer) }}" class="admin-link hover:underline">{{ $order->customer_name }}</a>
                                @else
                                    {{ $order->customer_name }}
                                @endif
                            </dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="w-15 shrink-0 font-semibold text-slate-500">Phone</dt>
                            <dd class="flex-1">
                                @if($order->customer)
                                    <a href="{{ route('admin.customers.show', $order->customer) }}" class="admin-link hover:underline">{{ $order->customer_phone }}</a>
                                @else
                                    {{ $order->customer_phone }}
                                @endif
                            </dd>
                        </div>
                        @if($order->customer_email)
                            <div class="flex gap-2">    
                                <dt class="w-15 shrink-0 font-semibold text-slate-500">Email</dt>
                                <dd class="flex-1">{{ $order->customer_email }}</dd>
                            </div>
                        @endif
                        <div class="flex gap-2">
                            <dt class="w-15 shrink-0 font-semibold text-slate-500">Địa chỉ</dt>
                            <dd class="flex-1">{{ $order->customer_address }}</dd>
                        </div>
                        @if($order->note)
                            <div class="flex gap-2">
                                <dt class="w-15 shrink-0 font-semibold text-slate-500">Ghi chú</dt>
                                <dd class="flex-1 italic text-slate-600">{{ $order->note }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
            <div class="x_panel">
                <div class="x_title"><h2>Thanh toán</h2></div>
                <div class="x_content text-sm space-y-2">
                    <div class="flex justify-between"><span>Tạm tính</span><span>{{ format_money($order->subtotal) }}</span></div>
                    <div class="flex justify-between"><span>Phí ship</span><span>{{ format_money($order->shipping_fee) }}</span></div>
                    <div class="flex justify-between"><span>Giảm giá</span><span>-{{ format_money($order->discount) }}</span></div>
                    <div class="flex justify-between border-t border-slate-200 pt-2 text-base font-bold"><span>Tổng</span><span class="text-gent-accent">{{ $order->formattedTotal() }}</span></div>
                </div>
            </div>
            <div class="x_panel">
                <div class="x_title"><h2>Cập nhật trạng thái</h2></div>
                <div class="x_content">
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                @foreach(\App\Models\Order::statusLabels() as $key => $label)
                                    <option value="{{ $key }}" @selected($order->status===$key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-full">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
