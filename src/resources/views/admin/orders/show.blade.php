@extends('admin.layouts.app')
@section('page-frame-class', 'admin-page-frame--order-show')
@section('title', 'Chi tiết đơn hàng')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Đơn hàng', 'url' => route('admin.orders.index')],
        ['label' => 'Chi tiết đơn hàng'],
    ])])
@endsection
@section('content')
    <div class="order-show-layout">
        <div class="x_panel order-show-block order-show-block--info">
            <div class="x_title"><h2>Thông tin đơn</h2></div>
            <div class="x_content text-sm">
                <dl class="space-y-2">
                    <div class="flex gap-2">
                        <dt class="w-24 shrink-0 font-semibold text-slate-500">Mã đơn</dt>
                        <dd class="flex-1 font-medium">{{ $order->order_code }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-24 shrink-0 font-semibold text-slate-500">Ngày đặt</dt>
                        <dd class="flex-1">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-24 shrink-0 font-semibold text-slate-500">Trạng thái</dt>
                        <dd class="flex-1">@include('admin.partials.status-badge', ['label' => $order->statusLabel(), 'class' => $order->statusBadgeClass()])</dd>
                    </div>
                </dl>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="order-status-inline-fields">
                    @csrf @method('PATCH')
                    <select name="status" class="form-select" aria-label="Trạng thái đơn hàng">
                        @foreach(\App\Models\Order::statusLabels() as $key => $label)
                            <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Cập nhật</button>
                </form>
            </div>
        </div>

        <div class="x_panel order-show-block order-show-block--items">
            <div class="x_title"><h2>Chi tiết đơn hàng</h2></div>
            <div class="x_content">
                <div class="table-responsive">
                    <table class="table table-striped order-show-items-table">
                        <thead>
                            <tr>
                                <th class="th-product-name">Tên sản phẩm</th>
                                <th class="th-qty">SL</th>
                                <th class="th-price">Đơn giá</th>
                                <th class="th-price">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="cell-product-name">
                                        @if($item->product)
                                            <a href="{{ route('admin.products.show', $item->product) }}" class="admin-link">{{ $item->product_name }}</a>
                                        @else
                                            {{ $item->product_name }}
                                        @endif
                                        @if($item->variant_label)
                                            <span class="text-xs text-red-600">({{ $item->variant_label }})</span>
                                        @endif
                                    </td>
                                    <td class="cell-qty">{{ $item->quantity }}</td>
                                    <td class="cell-unit-price">{{ format_money($item->unit_price) }}</td>
                                    <td class="cell-line-total">{{ format_money($item->line_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="x_panel order-show-block order-show-block--customer">
            <div class="x_title"><h2>Khách hàng</h2></div>
            <div class="x_content text-sm">
                <dl class="space-y-2">
                    <div class="flex gap-2">
                        <dt class="w-24 shrink-0 font-semibold text-slate-500">Tên</dt>
                        <dd class="flex-1">
                            @if($order->customer)
                                <a href="{{ route('admin.customers.show', $order->customer) }}" class="admin-link hover:underline">{{ $order->customer_name }}</a>
                            @else
                                {{ $order->customer_name }}
                            @endif
                        </dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-24 shrink-0 font-semibold text-slate-500">Phone</dt>
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
                            <dt class="w-24 shrink-0 font-semibold text-slate-500">Email</dt>
                            <dd class="flex-1">{{ $order->customer_email }}</dd>
                        </div>
                    @endif
                    <div class="flex gap-2">
                        <dt class="w-24 shrink-0 font-semibold text-slate-500">Địa chỉ</dt>
                        <dd class="flex-1">{{ $order->customer_address }}</dd>
                    </div>
                    @if($order->note)
                        <div class="flex gap-2">
                            <dt class="w-24 shrink-0 font-semibold text-slate-500">Ghi chú</dt>
                            <dd class="flex-1 italic text-slate-600">{{ $order->note }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="x_panel order-show-block order-show-block--payment">
            <div class="x_title"><h2>Thanh toán</h2></div>
            <div class="x_content text-sm space-y-2">
                <div class="flex justify-between"><span>Tạm tính</span><span>{{ format_money($order->subtotal) }}</span></div>
                <div class="flex justify-between"><span>Phí ship</span><span>{{ format_money($order->shipping_fee) }}</span></div>
                <div class="flex justify-between"><span>Giảm giá</span><span>-{{ format_money($order->discount) }}</span></div>
                <div class="flex justify-between border-t border-slate-200 pt-2 text-base font-bold"><span>Tổng</span><span class="text-gent-accent">{{ $order->formattedTotal() }}</span></div>
            </div>
        </div>
    </div>
@endsection
