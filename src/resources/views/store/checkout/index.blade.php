@extends('store.layouts.app')

@section('title', 'Đặt hàng — ' . store_setting('name'))

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
        <h1 class="mb-8 text-2xl font-bold text-slate-900">Thông tin đặt hàng</h1>

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('checkout.store') }}" class="space-y-4 rounded-lg border border-slate-200 p-6">
                    @csrf
                    <div><label class="mb-1 block text-sm font-medium">Họ tên *</label><input name="name" value="{{ old('name') }}" required class="store-form-control"></div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div><label class="mb-1 block text-sm font-medium">Số điện thoại *</label><input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="0912 345 678" class="store-form-control"></div>
                        <div><label class="mb-1 block text-sm font-medium">Email</label><input type="email" name="email" value="{{ old('email') }}" class="store-form-control"></div>
                    </div>
                    <div><label class="mb-1 block text-sm font-medium">Địa chỉ giao hàng *</label><textarea name="address" rows="3" required class="store-form-control">{{ old('address') }}</textarea></div>
                    <div><label class="mb-1 block text-sm font-medium">Ghi chú</label><textarea name="note" rows="2" class="store-form-control">{{ old('note') }}</textarea></div>
                    <div class="rounded bg-slate-50 p-4 text-sm text-slate-600">
                        <strong>Thanh toán khi nhận hàng (COD)</strong> — Không cần thanh toán trước.
                    </div>
                    <button type="submit" class="w-full rounded bg-accent-red py-3 text-sm font-bold uppercase text-white hover:bg-red-700">Xác nhận đặt hàng</button>
                </form>
            </div>
            <div class="space-y-4">
                <div class="rounded-lg border border-slate-200 p-6">
                    <h2 class="mb-4 font-bold">Đơn hàng</h2>
                    @foreach ($items as $item)
                        <div class="mb-3 flex justify-between text-sm">
                            <span>{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                            <span>{{ format_money($item['unit_price'] * $item['quantity']) }}</span>
                        </div>
                    @endforeach
                    <div class="space-y-2 border-t border-slate-200 pt-3 text-sm">
                        <div class="flex justify-between"><span>Tạm tính</span><span>{{ format_money($subtotal) }}</span></div>
                        <div class="flex justify-between"><span>Phí ship</span><span>{{ format_money($shippingFee) }}</span></div>
                        @if ($discount > 0)
                            <div class="flex justify-between text-green-700"><span>Giảm giá</span><span>-{{ format_money($discount) }}</span></div>
                        @endif
                        <div class="flex justify-between text-base font-bold"><span>Tổng</span><span class="text-brand">{{ format_money($total) }}</span></div>
                    </div>
                </div>
                <form method="POST" action="{{ route('checkout.promo') }}" class="flex gap-2">
                    @csrf
                    <input type="text" name="promo_code" value="{{ $promoCode }}" placeholder="Mã khuyến mãi" class="store-form-control flex-1 uppercase">
                    <button class="shrink-0 rounded bg-brand px-4 py-2 text-sm font-semibold text-white">Áp dụng</button>
                </form>
            </div>
        </div>
    </div>
@endsection
