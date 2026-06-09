@extends('store.layouts.app')

@section('title', 'Giỏ hàng — ' . store_setting('name'))

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
        <h1 class="mb-8 text-2xl font-bold text-slate-900">Giỏ hàng</h1>

        @if (session('success'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif

        @if ($items->isEmpty())
            <div class="rounded-lg border border-slate-200 bg-slate-50 px-6 py-16 text-center">
                <p class="text-slate-600">Giỏ hàng của bạn đang trống.</p>
                <a href="{{ route('products.index') }}" class="mt-4 inline-block rounded bg-brand px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-dark">Tiếp tục mua sắm</a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-4">
                    @foreach ($items as $item)
                        <div class="flex gap-4 rounded-lg border border-slate-200 p-4">
                            @if ($item['image'])
                                <img src="{{ store_media_url($item['image'], 'thumbnail') }}" alt="" class="h-20 w-20 rounded object-cover">
                            @endif
                            <div class="flex-1">
                                <h3 class="font-semibold text-slate-900">{{ $item['name'] }}</h3>
                                @if ($item['variant_label'])
                                    <p class="text-sm text-slate-500">{{ $item['variant_label'] }}</p>
                                @endif
                                <p class="mt-1 font-medium text-brand">{{ format_money($item['unit_price']) }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <form method="POST" action="{{ route('cart.update') }}" class="flex items-center gap-2">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="key" value="{{ $item['key'] }}">
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="99" class="w-16 rounded border-slate-300 text-sm">
                                    <button class="text-sm text-brand hover:underline">Cập nhật</button>
                                </form>
                                <form method="POST" action="{{ route('cart.remove', $item['key']) }}">
                                    @csrf @method('DELETE')
                                    <button class="text-sm text-red-600 hover:underline">Xóa</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-6">
                    <h2 class="mb-4 font-bold text-slate-900">Tóm tắt</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Tạm tính</span><span>{{ format_money($subtotal) }}</span></div>
                        <div class="flex justify-between"><span>Phí vận chuyển</span><span>{{ format_money($shippingFee) }}</span></div>
                        <div class="flex justify-between border-t border-slate-200 pt-2 text-base font-bold"><span>Tổng</span><span class="text-brand">{{ format_money($subtotal + $shippingFee) }}</span></div>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="mt-6 block rounded bg-accent-red py-3 text-center text-sm font-bold uppercase text-white hover:bg-red-700">Thanh toán</a>
                </div>
            </div>
        @endif
    </div>
@endsection
