@extends('store.layouts.app')

@section('title', 'Giỏ hàng — ' . store_setting('name'))

@section('content')
    @php
        $itemCount = $items->sum('quantity');
        $grandTotal = $subtotal + $shippingFee;
        $freeShipThreshold = 350000;
        $shipProgress = $subtotal >= $freeShipThreshold ? 100 : min(100, ($subtotal / $freeShipThreshold) * 100);
        $shipRemaining = max(0, $freeShipThreshold - $subtotal);
    @endphp

    <div class="bg-brand py-8 text-white sm:py-10">
        <div class="store-container">
            <nav class="mb-2 text-sm text-white/75">
                <a href="{{ route('home') }}" class="hover:text-white">Trang chủ</a>
                <span class="mx-2">/</span>
                <span class="text-white">Giỏ hàng</span>
            </nav>
            <h1 class="text-2xl font-bold sm:text-3xl">Giỏ hàng</h1>
            @if (! $items->isEmpty())
                <p class="mt-1 text-sm text-white/80">{{ $itemCount }} sản phẩm</p>
            @endif
        </div>
    </div>

    <div class="store-container py-8 lg:py-10 {{ $items->isEmpty() ? '' : 'pb-24 lg:pb-10' }}">
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($items->isEmpty())
            <div class="mx-auto max-w-md rounded-xl border border-slate-200 bg-white px-6 py-14 text-center">
                <p class="text-slate-600">Giỏ hàng của bạn đang trống.</p>
                <a href="{{ route('products.index') }}" class="mt-5 inline-block rounded-full bg-brand px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-dark">
                    Tiếp tục mua sắm
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-3">
                <div class="space-y-4 lg:col-span-2">
                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                        {{-- Desktop: bảng HTML — header và body luôn thẳng cột --}}
                        <div class="hidden lg:block">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <th class="px-5 py-3.5 font-semibold">Sản phẩm</th>
                                        <th class="w-32 px-4 py-3.5 text-right font-semibold">Đơn giá</th>
                                        <th class="w-40 px-4 py-3.5 text-center font-semibold">Số lượng</th>
                                        <th class="w-32 px-4 py-3.5 text-right font-semibold">Thành tiền</th>
                                        <th class="w-14 px-3 py-3.5"><span class="sr-only">Xóa</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($items as $item)
                                        @php
                                            $lineTotal = $item['unit_price'] * $item['quantity'];
                                            $productUrl = ! empty($item['slug']) ? route('products.show', $item['slug']) : route('products.index');
                                        @endphp
                                        <tr x-data="cartLineItem({{ $item['quantity'] }})">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-4">
                                                    <a href="{{ $productUrl }}" class="block h-[4.5rem] w-[4.5rem] shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                                        @if ($item['image'])
                                                            <img src="{{ store_media_url($item['image'], 'thumbnail') }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                                                        @else
                                                            <div class="flex h-full w-full items-center justify-center text-slate-300">
                                                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                            </div>
                                                        @endif
                                                    </a>
                                                    <div class="min-w-0">
                                                        <a href="{{ $productUrl }}" class="font-semibold text-slate-900 hover:text-brand">{{ $item['name'] }}</a>
                                                        @if ($item['variant_label'])
                                                            <p class="mt-1 text-sm text-slate-500">{{ $item['variant_label'] }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 text-right text-sm font-medium text-slate-700">{{ format_money($item['unit_price']) }}</td>
                                            <td class="px-4 text-center">
                                                @include('store.cart._qty-form', ['item' => $item])
                                            </td>
                                            <td class="px-4 text-right text-sm font-bold text-slate-900">{{ format_money($lineTotal) }}</td>
                                            <td class="px-3 text-center">
                                                @include('store.cart._remove-btn', ['item' => $item, 'style' => 'icon'])
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile: thẻ từng sản phẩm --}}
                        <div class="divide-y divide-slate-100 lg:hidden">
                            @foreach ($items as $item)
                                @php
                                    $lineTotal = $item['unit_price'] * $item['quantity'];
                                    $productUrl = ! empty($item['slug']) ? route('products.show', $item['slug']) : route('products.index');
                                @endphp
                                <article class="p-4 sm:p-5" x-data="cartLineItem({{ $item['quantity'] }})">
                                    <div class="flex gap-3 sm:gap-4">
                                        <a href="{{ $productUrl }}" class="block h-20 w-20 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                            @if ($item['image'])
                                                <img src="{{ store_media_url($item['image'], 'thumbnail') }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-slate-300">
                                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                            @endif
                                        </a>
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ $productUrl }}" class="font-semibold text-slate-900 hover:text-brand">{{ $item['name'] }}</a>
                                            @if ($item['variant_label'])
                                                <p class="mt-1 text-sm text-slate-500">{{ $item['variant_label'] }}</p>
                                            @endif
                                            <div class="mt-2 flex items-baseline justify-between gap-2">
                                                <span class="text-sm text-slate-500">{{ format_money($item['unit_price']) }}</span>
                                                <span class="font-bold text-brand">{{ format_money($lineTotal) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                                        @include('store.cart._qty-form', ['item' => $item])
                                        @include('store.cart._remove-btn', ['item' => $item, 'style' => 'text'])
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-brand hover:underline">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Tiếp tục mua sắm
                    </a>
                </div>

                {{-- Tóm tắt — nút thanh toán desktop; mobile dùng thanh cố định bên dưới --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 lg:sticky lg:top-24">
                    <h2 class="text-lg font-bold text-slate-900">Tóm tắt đơn hàng</h2>

                    @if ($subtotal < $freeShipThreshold)
                        <div class="mt-3 rounded-lg bg-brand-muted px-3 py-2.5 text-xs text-brand-dark">
                            Mua thêm <strong>{{ format_money($shipRemaining) }}</strong> để miễn phí ship
                            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-white/60">
                                <div class="h-full rounded-full bg-brand" style="width: {{ $shipProgress }}%"></div>
                            </div>
                        </div>
                    @else
                        <p class="mt-3 rounded-lg bg-green-50 px-3 py-2 text-xs font-medium text-green-800">
                            ✓ Miễn phí vận chuyển
                        </p>
                    @endif

                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-600">Tạm tính</dt>
                            <dd class="whitespace-nowrap font-medium">{{ format_money($subtotal) }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-600">Phí vận chuyển</dt>
                            <dd class="whitespace-nowrap font-medium">
                                @if ($shippingFee === 0)
                                    <span class="text-green-700">Miễn phí</span>
                                @else
                                    {{ format_money($shippingFee) }}
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between gap-3 border-t border-slate-200 pt-4">
                            <dt class="font-bold text-slate-900">Tổng cộng</dt>
                            <dd class="whitespace-nowrap text-xl font-bold text-brand">{{ format_money($grandTotal) }}</dd>
                        </div>
                    </dl>

                    <p class="mt-2 text-xs text-slate-500">Thanh toán khi nhận hàng (COD)</p>

                    <a href="{{ route('checkout.index') }}" class="mt-5 hidden w-full rounded-lg bg-accent-red py-3.5 text-center text-sm font-bold uppercase text-white hover:bg-red-700 lg:block">
                        Tiến hành thanh toán
                    </a>
                </div>
            </div>

            {{-- Mobile: thanh thanh toán cố định, không bị icon liên hệ che --}}
            <div class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white px-4 py-3 shadow-[0_-4px_20px_rgba(15,23,42,0.1)] lg:hidden">
                <div class="store-container flex items-center gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-slate-500">Tổng cộng</p>
                        <p class="truncate text-lg font-bold text-brand">{{ format_money($grandTotal) }}</p>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="shrink-0 rounded-lg bg-accent-red px-6 py-3 text-sm font-bold uppercase text-white hover:bg-red-700">
                        Thanh toán
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
