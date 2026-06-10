@extends('store.layouts.app')

@section('title', 'Đặt hàng — ' . store_setting('name'))

@section('content')
    <div class="store-container py-8 lg:py-12">
        <div
            x-data="checkoutForm(@js([
                'name' => old('name', ''),
                'phone' => old('phone', ''),
                'email' => old('email', ''),
                'address' => old('address', ''),
                'note' => old('note', ''),
                'step' => ($errors->any() && old('name')) ? 2 : 1,
            ]))"
        >
            <div class="mb-6 space-y-4">
                <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-brand hover:underline">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Quay lại giỏ hàng
                </a>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h1 class="text-xl font-bold text-slate-900 sm:text-2xl" x-text="step === 1 ? 'Thông tin đặt hàng' : 'Xác nhận đơn hàng'">Thông tin đặt hàng</h1>
                    <div class="flex shrink-0 items-center gap-1.5 text-xs sm:gap-2 sm:text-sm">
                        <span class="whitespace-nowrap rounded-full px-2.5 py-1 font-medium sm:px-3" :class="step === 1 ? 'bg-brand text-white' : 'bg-slate-100 text-slate-600'">1. Thông tin</span>
                        <span class="text-slate-300">→</span>
                        <span class="whitespace-nowrap rounded-full px-2.5 py-1 font-medium sm:px-3" :class="step === 2 ? 'bg-brand text-white' : 'bg-slate-100 text-slate-600'">2. Xác nhận</span>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-3">
                {{-- Đơn hàng: trên mobile hiển thị trước form --}}
                <div class="order-1 space-y-4 lg:order-2">
                    @include('store.checkout._order-summary')

                    <form x-show="step === 1" x-cloak method="POST" action="{{ route('checkout.promo') }}" class="flex flex-col gap-2 sm:flex-row">
                        @csrf
                        <input type="text" name="promo_code" value="{{ $promoCode }}" placeholder="Mã khuyến mãi" class="store-form-control min-w-0 flex-1 uppercase">
                        <button class="shrink-0 rounded bg-brand px-4 py-2 text-sm font-semibold text-white sm:whitespace-nowrap">Áp dụng</button>
                    </form>
                </div>

                <div class="order-2 lg:order-1 lg:col-span-2">
                    <form
                        x-ref="checkoutForm"
                        method="POST"
                        action="{{ route('checkout.store') }}"
                        class="rounded-lg border border-slate-200 p-4 sm:p-6"
                    >
                        @csrf

                        {{-- Bước 1 --}}
                        <div x-show="step === 1" class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium">Họ tên *</label>
                                <input x-model="name" name="name" required class="store-form-control">
                            </div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium">Số điện thoại *</label>
                                    <input x-model="phone" type="tel" name="phone" required placeholder="0912345678" class="store-form-control">
                                    <p x-show="phoneError" x-text="phoneError" class="mt-1 text-sm text-red-600"></p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium">Email</label>
                                    <input x-model="email" type="email" name="email" class="store-form-control">
                                </div>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium">Địa chỉ giao hàng *</label>
                                <textarea x-model="address" name="address" rows="3" required class="store-form-control"></textarea>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium">Ghi chú</label>
                                <textarea x-model="note" name="note" rows="2" class="store-form-control"></textarea>
                            </div>
                            <div class="rounded bg-slate-50 p-4 text-sm text-slate-600">
                                <strong>Thanh toán khi nhận hàng (COD)</strong> — Không cần thanh toán trước.
                            </div>
                            <button type="button" @click="goReview()" class="w-full rounded bg-accent-red py-3 text-sm font-bold uppercase text-white hover:bg-red-700">
                                Mua hàng
                            </button>
                        </div>

                        {{-- Bước 2 --}}
                        <div x-show="step === 2" x-cloak class="space-y-6">
                            <p class="text-sm text-slate-600">Vui lòng kiểm tra thông tin trước khi đặt hàng.</p>

                            <div class="overflow-hidden rounded-lg border border-slate-200">
                                <div class="border-b border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-800">Thông tin giao hàng</div>
                                <dl class="divide-y divide-slate-100 text-sm">
                                    <div class="px-4 py-3 sm:flex sm:gap-4">
                                        <dt class="mb-0.5 shrink-0 font-medium text-slate-500 sm:mb-0 sm:w-28 sm:font-normal">Họ tên</dt>
                                        <dd class="min-w-0 font-medium text-slate-800 sm:flex-1" x-text="name"></dd>
                                    </div>
                                    <div class="px-4 py-3 sm:flex sm:gap-4">
                                        <dt class="mb-0.5 shrink-0 font-medium text-slate-500 sm:mb-0 sm:w-28 sm:font-normal">Điện thoại</dt>
                                        <dd class="min-w-0 font-medium text-slate-800 sm:flex-1" x-text="phone"></dd>
                                    </div>
                                    <div class="px-4 py-3 sm:flex sm:gap-4">
                                        <dt class="mb-0.5 shrink-0 font-medium text-slate-500 sm:mb-0 sm:w-28 sm:font-normal">Email</dt>
                                        <dd class="min-w-0 break-all text-slate-800 sm:flex-1" x-text="email || '—'"></dd>
                                    </div>
                                    <div class="px-4 py-3 sm:flex sm:gap-4">
                                        <dt class="mb-0.5 shrink-0 font-medium text-slate-500 sm:mb-0 sm:w-28 sm:font-normal">Địa chỉ</dt>
                                        <dd class="min-w-0 whitespace-pre-wrap text-slate-800 sm:flex-1" x-text="address"></dd>
                                    </div>
                                    <div class="px-4 py-3 sm:flex sm:gap-4" x-show="note.trim()">
                                        <dt class="mb-0.5 shrink-0 font-medium text-slate-500 sm:mb-0 sm:w-28 sm:font-normal">Ghi chú</dt>
                                        <dd class="min-w-0 whitespace-pre-wrap text-slate-800 sm:flex-1" x-text="note"></dd>
                                    </div>
                                    <div class="px-4 py-3 sm:flex sm:gap-4">
                                        <dt class="mb-0.5 shrink-0 font-medium text-slate-500 sm:mb-0 sm:w-28 sm:font-normal">Thanh toán</dt>
                                        <dd class="min-w-0 text-slate-800 sm:flex-1">COD — Thanh toán khi nhận hàng</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row">
                                <button type="button" @click="backToEdit()" class="w-full rounded border border-slate-300 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:flex-1">
                                    Quay lại
                                </button>
                                <button type="submit" class="w-full rounded bg-accent-red py-3 text-sm font-bold uppercase text-white hover:bg-red-700 sm:flex-1">
                                    Xác nhận đặt hàng
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
