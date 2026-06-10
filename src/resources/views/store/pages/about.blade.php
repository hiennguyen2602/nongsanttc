@extends('store.layouts.app')

@section('title', 'Về chúng tôi — ' . store_setting('name'))

@section('content')
    @php
        $aboutMainUrl = store_media_url(store_setting('about_main'));
        $aboutSmallUrl = store_media_url(store_setting('about_small'));
    @endphp

    {{-- Hero nhỏ --}}
    <section class="relative flex min-h-[40vh] items-center justify-center overflow-hidden {{ $aboutMainUrl ? '' : 'bg-brand-dark' }}">
        @if ($aboutMainUrl)
            <img src="{{ $aboutMainUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover">
        @endif
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 store-container text-center text-white">
            <p class="font-display mb-2 italic text-amber-200/90">Về chúng tôi</p>
            <h1 class="text-3xl font-bold sm:text-4xl">Câu chuyện {{ store_setting('name') }}</h1>
        </div>
    </section>

    <section class="py-16 lg:py-24">
        <div class="store-container grid grid-cols-1 items-center gap-12 lg:grid-cols-2">
            <div>
                <h2 class="mb-4 text-2xl font-bold text-slate-900">Sứ mệnh của chúng tôi</h2>
                <p class="mb-4 text-slate-600 leading-relaxed">
                    {{ store_setting('name') }} cam kết mang đến nông sản sạch, an toàn và đậm đà bản sắc Việt. Chúng tôi làm việc trực tiếp với nông dân và hợp tác xã, minh bạch hóa toàn bộ chuỗi cung ứng từ ruộng đến tay người tiêu dùng.
                </p>
                <p class="text-slate-600 leading-relaxed">
                    Mỗi sản phẩm đều được kiểm tra chất lượng, đóng gói cẩn thận và giao đến tay khách hàng với sự tận tâm cao nhất.
                </p>
            </div>
            @if ($aboutSmallUrl)
                <img src="{{ $aboutSmallUrl }}" alt="Nông trại" class="aspect-[4/3] w-full rounded-xl object-cover shadow-lg">
            @else
                <div class="aspect-[4/3] w-full overflow-hidden rounded-xl shadow-lg ring-1 ring-slate-200/50">
                    @include('store.partials.media-placeholder')
                </div>
            @endif
        </div>
    </section>

    <section class="bg-slate-50 py-16">
        <div class="store-container text-center">
            <h2 class="mb-10 text-2xl font-bold text-slate-900">Giá trị cốt lõi</h2>
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-3">
                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-brand/10 text-brand">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="mb-2 font-bold text-slate-800">Chất lượng</h3>
                    <p class="text-sm text-slate-600">Kiểm soát nghiêm ngặt từ nguồn gốc đến thành phẩm</p>
                </div>
                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-brand/10 text-brand">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <h3 class="mb-2 font-bold text-slate-800">Tận tâm</h3>
                    <p class="text-sm text-slate-600">Luôn lắng nghe và phục vụ khách hàng chu đáo</p>
                </div>
                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-brand/10 text-brand">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                    </div>
                    <h3 class="mb-2 font-bold text-slate-800">Bền vững</h3>
                    <p class="text-sm text-slate-600">Hỗ trợ nông dân bản địa, bảo vệ môi trường</p>
                </div>
            </div>
        </div>
    </section>
@endsection
