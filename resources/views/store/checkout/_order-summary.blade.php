<div class="rounded-lg border border-slate-200 p-4 sm:p-6">
    <h2 class="mb-4 font-bold text-slate-900">Đơn hàng</h2>
    <div class="divide-y divide-slate-100">
        @foreach ($items as $item)
            <div class="flex items-start justify-between gap-3 py-3 text-sm first:pt-0 last:pb-0">
                <div class="min-w-0 flex-1 leading-snug">
                    <p class="font-medium text-slate-800">{{ $item['name'] }}</p>
                    @if (! empty($item['variant_label']))
                        <p class="mt-0.5 text-slate-500">{{ $item['variant_label'] }}</p>
                    @endif
                    <p class="mt-0.5 text-slate-500">× {{ $item['quantity'] }}</p>
                </div>
                <span class="shrink-0 whitespace-nowrap font-medium text-slate-800">{{ format_money($item['unit_price'] * $item['quantity']) }}</span>
            </div>
        @endforeach
    </div>
    <div class="mt-4 space-y-2 border-t border-slate-200 pt-4 text-sm">
        <div class="flex items-center justify-between gap-3"><span class="shrink-0">Tạm tính</span><span class="whitespace-nowrap">{{ format_money($subtotal) }}</span></div>
        <div class="flex items-center justify-between gap-3"><span class="shrink-0">Phí ship</span><span class="whitespace-nowrap">{{ format_money($shippingFee) }}</span></div>
        @if ($discount > 0)
            <div class="flex items-center justify-between gap-3 text-green-700">
                <span class="min-w-0 truncate">Giảm giá@if ($promoCode) ({{ $promoCode }})@endif</span>
                <span class="shrink-0 whitespace-nowrap">-{{ format_money($discount) }}</span>
            </div>
        @endif
        <div class="flex items-center justify-between gap-3 text-base font-bold"><span>Tổng</span><span class="whitespace-nowrap text-brand">{{ format_money($total) }}</span></div>
    </div>
</div>
