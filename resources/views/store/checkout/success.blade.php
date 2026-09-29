@extends('store.layouts.app')

@section('title', 'Đặt hàng thành công — ' . store_setting('name'))
@section('robots', 'noindex,nofollow')

@section('content')
    <div class="store-container store-container--compact py-16 text-center">
        <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Đặt hàng thành công!</h1>
        <p class="mt-2 text-slate-600">Cảm ơn bạn đã mua hàng. Chúng tôi sẽ liên hệ xác nhận đơn hàng sớm nhất.</p>
        <p class="mt-4 text-lg font-semibold text-brand">Mã đơn: {{ $order->order_code }}</p>
        <p class="mt-1 text-slate-600">Tổng thanh toán: {{ $order->formattedTotal() }}</p>
        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
            <a href="{{ route('products.index') }}" class="rounded bg-brand px-6 py-2.5 text-sm font-semibold text-white">Tiếp tục mua sắm</a>
            <a href="{{ route('home') }}" class="rounded border border-slate-300 px-6 py-2.5 text-sm font-semibold text-slate-700">Về trang chủ</a>
        </div>
    </div>
@endsection
