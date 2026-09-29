<x-mail::message>
# Đơn hàng mới: {{ $order->order_code }}

**Khách hàng:** {{ $order->customer_name }}  
**Điện thoại:** {{ $order->customer_phone }}  
@if ($order->customer_email)
**Email:** {{ $order->customer_email }}  
@endif
**Địa chỉ:** {{ $order->customer_address }}  
@if ($order->note)
**Ghi chú:** {{ $order->note }}  
@endif

## Sản phẩm

@foreach ($order->items as $item)
- {{ $item->product_name }}@if ($item->variant_label) ({{ $item->variant_label }})@endif × {{ $item->quantity }} — {{ format_money($item->line_total) }}
@endforeach

**Tạm tính:** {{ format_money($order->subtotal) }}  
**Phí giao hàng:** {{ format_money($order->shipping_fee) }}  
@if ($order->discount > 0)
**Giảm giá:** -{{ format_money($order->discount) }}@if ($order->promo_code) ({{ $order->promo_code }})@endif  
@endif
**Tổng thanh toán:** {{ $order->formattedTotal() }}

<x-mail::button :url="$orderUrl">
Xem đơn hàng
</x-mail::button>

Trân trọng,<br>
{{ config('app.name') }}
</x-mail::message>
