@extends('store.layouts.app')

@section('title', 'Liên hệ — ' . store_setting('name'))
@section('meta_description', seo_meta_description(store_setting('contact_meta_description')))
@section('canonical', route('contact', absolute: true))

@push('json-ld')
    @include('partials.seo.breadcrumb', ['crumbs' => [
        ['name' => 'Trang chủ', 'url' => route('home', absolute: true)],
        ['name' => 'Liên hệ', 'url' => route('contact', absolute: true)],
    ]])
    @php
        $localBusiness = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => store_setting('company_name') ?: store_setting('name'),
            'url' => route('home', absolute: true),
            'description' => seo_meta_description(store_setting('contact_meta_description')),
            'image' => store_media_url(store_setting('hero_desktop'), 'large'),
        ];
        if (filled(store_setting('phone'))) {
            $localBusiness['telephone'] = store_setting('phone');
        }
        if (filled(store_setting('email'))) {
            $localBusiness['email'] = store_setting('email');
        }
        if (filled(store_setting('address'))) {
            $localBusiness['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => store_setting('address'),
                'addressCountry' => 'VN',
            ];
        }
        if (filled(store_setting('google_maps_url'))) {
            $localBusiness['hasMap'] = store_setting('google_maps_url');
        }
    @endphp
    @include('partials.seo.json-ld', ['data' => array_filter($localBusiness, fn ($value) => $value !== null && $value !== '')])
@endpush

@section('content')
    <section class="py-12 lg:py-16">
        <div class="store-container grid grid-cols-1 gap-10 lg:grid-cols-5 lg:gap-12">
            {{-- Thông tin --}}
            <div class="space-y-6 lg:col-span-2">
                <div>
                    <h1 class="mb-6 text-2xl font-bold text-slate-900">Liên hệ</h1>
                    @if (store_setting('company_name'))
                        <p class="mb-6 text-base font-semibold text-slate-800">{{ store_setting('company_name') }}</p>
                    @endif
                    <ul class="space-y-4 text-sm text-slate-600">
                        @if (store_setting('address'))
                        <li class="flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand/10 text-brand">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            <span class="min-w-0 flex-1 leading-snug">{{ store_setting('address') }}</span>
                        </li>
                        @endif
                        @if (store_setting('phone'))
                        <li class="flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand/10 text-brand">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </span>
                            <a href="tel:{{ preg_replace('/\s+/', '', store_setting('phone')) }}" class="min-w-0 flex-1 font-medium leading-snug text-brand hover:underline">{{ store_setting('phone') }}</a>
                        </li>
                        @endif
                        @if (store_setting('email'))
                        <li class="flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand/10 text-brand">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <a href="mailto:{{ store_setting('email') }}" class="min-w-0 flex-1 break-all font-medium leading-snug text-brand hover:underline">{{ store_setting('email') }}</a>
                        </li>
                        @endif
                    </ul>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if (store_setting('zalo'))
                    <a href="{{ store_setting('zalo') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600">Zalo</a>
                    @endif
                    @if (store_setting('messenger'))
                    <a href="{{ store_setting('messenger') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full bg-[#0084FF] px-4 py-2 text-sm font-medium text-white hover:bg-[#0073e6]">Messenger</a>
                    @endif
                    @if (store_setting('google_maps_url'))
                    <a href="{{ store_setting('google_maps_url') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full border border-brand/30 px-4 py-2 text-sm font-medium text-brand hover:bg-brand/5">Xem bản đồ</a>
                    @endif
                </div>

                @if (store_setting('google_maps_embed'))
                <div class="overflow-hidden rounded-xl ring-1 ring-slate-200">
                    <div class="aspect-[4/3] w-full [&>iframe]:h-full [&>iframe]:w-full">
                        {!! store_setting('google_maps_embed') !!}
                    </div>
                </div>
                @endif
            </div>

            {{-- Form --}}
            <div class="lg:col-span-3">
                @if (session('success'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
                    @csrf
                    <h2 class="text-lg font-bold text-slate-900">Gửi tin nhắn</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Họ tên *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="store-form-control">
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Số điện thoại *</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="0912345678" class="store-form-control">
                            @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="store-form-control">
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Chủ đề</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Đặt hàng, tư vấn sản phẩm..." class="store-form-control">
                        @error('subject')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Nội dung *</label>
                        <textarea name="message" rows="5" required class="store-form-control">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-accent-red py-3 text-sm font-bold uppercase tracking-wide text-white hover:bg-red-700 sm:w-auto sm:px-10">Gửi liên hệ</button>
                </form>
            </div>
        </div>
    </section>
@endsection
