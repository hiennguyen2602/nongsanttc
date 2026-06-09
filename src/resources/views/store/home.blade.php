@extends('store.layouts.app')

@section('title', config('store.name') . ' — ' . config('store.tagline'))

@push('head')
    <link rel="preload" as="image" href="{{ store_media_url(config('store.images.hero')) }}">
@endpush

@section('content')
    {{-- Hero — parallax + entrance animation --}}
    <section
        x-data="heroSection()"
        class="relative flex min-h-[75vh] items-center justify-center overflow-hidden lg:min-h-[90vh]"
    >
        <div
            class="absolute inset-0 scale-105 will-change-transform"
            :style="`transform: translateY(${parallax}px) scale(1.05)`"
        >
            <img
                src="{{ store_media_url(store_setting('hero_desktop', config('store.images.hero')), 'large') }}"
                srcset="{{ store_media_url(store_setting('hero_mobile', store_setting('hero_desktop', config('store.images.hero'))), 'large') }} 768w, {{ store_media_url(store_setting('hero_desktop', config('store.images.hero')), 'large') }} 1920w"
                sizes="100vw"
                alt="{{ store_setting('name') }}"
                class="h-full w-full object-cover"
                fetchpriority="high"
                decoding="async"
                width="1920"
                height="1080"
            >
        </div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/50 to-black/70"></div>

        <div class="relative z-10 mx-auto max-w-4xl px-4 text-center text-white">
            <p class="hero-animate font-display mb-3 text-lg italic text-harvest sm:text-xl">{{ config('store.name') }}</p>
            <h1 class="hero-animate hero-animate-delay-1 mb-4 text-3xl font-bold leading-tight sm:text-5xl lg:text-6xl">
                Trải nghiệm nông sản<br><span class="text-brand-light">sạch & chân thực</span>
            </h1>
            <p class="hero-animate hero-animate-delay-2 mx-auto mb-8 max-w-2xl text-sm leading-relaxed text-white/90 sm:text-base">
                {{ config('store.tagline') }}. Từ thửa ruộng xanh đến bàn ăn gia đình — chất lượng minh bạch, nguồn gốc rõ ràng.
            </p>
            <div class="hero-animate hero-animate-delay-3 flex flex-wrap justify-center gap-3">
                <a href="{{ route('products.index') }}" class="group inline-flex items-center gap-2 rounded-full bg-harvest px-7 py-3.5 text-sm font-semibold uppercase tracking-wide text-white shadow-lg shadow-harvest/30 transition hover:bg-harvest-dark hover:shadow-xl">
                    Khám phá sản phẩm
                    <svg class="h-4 w-4 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="{{ route('about') }}" class="rounded-full border-2 border-white/70 px-7 py-3.5 text-sm font-semibold uppercase tracking-wide text-white backdrop-blur-sm transition hover:border-white hover:bg-white/10">
                    Câu chuyện thương hiệu
                </a>
            </div>
        </div>

        {{-- Scroll hint --}}
        <a href="#noi-dung" class="scroll-hint absolute bottom-8 left-1/2 z-10 flex -translate-x-1/2 flex-col items-center gap-1 text-white/70 transition hover:text-white">
            <span class="text-[10px] uppercase tracking-[0.2em]">Cuộn xuống</span>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
        </a>
    </section>

    {{-- Trust stats — stagger reveal + counter --}}
    <section id="noi-dung" class="scroll-section relative z-10 -mt-1 border-b border-brand/10 bg-white shadow-[0_-20px_40px_-20px_rgba(47,115,72,0.15)]">
        <div class="mx-auto grid max-w-7xl grid-cols-2 divide-x divide-slate-100 px-4 py-8 sm:grid-cols-4 sm:px-6 sm:py-10" data-reveal-group>
            <div x-data="statCounter(500, '+')" data-reveal="fade-up" class="px-4 text-center">
                <p class="text-2xl font-bold text-brand sm:text-3xl" x-text="current + suffix">0</p>
                <p class="mt-1 text-xs text-slate-500 sm:text-sm">Khách hàng tin dùng</p>
            </div>
            <div x-data="statCounter(120, '+')" data-reveal="fade-up" class="px-4 text-center">
                <p class="text-2xl font-bold text-brand sm:text-3xl" x-text="current + suffix">0</p>
                <p class="mt-1 text-xs text-slate-500 sm:text-sm">Sản phẩm nông sản</p>
            </div>
            <div x-data="statCounter(50, '+')" data-reveal="fade-up" class="px-4 text-center">
                <p class="text-2xl font-bold text-brand sm:text-3xl" x-text="current + suffix">0</p>
                <p class="mt-1 text-xs text-slate-500 sm:text-sm">Hộ nông dân liên kết</p>
            </div>
            <div x-data="statCounter(15, '')" data-reveal="fade-up" class="px-4 text-center">
                <p class="text-2xl font-bold text-brand sm:text-3xl"><span x-text="current">0</span> năm</p>
                <p class="mt-1 text-xs text-slate-500 sm:text-sm">Kinh nghiệm phân phối</p>
            </div>
        </div>
    </section>

    {{-- Giới thiệu --}}
    <section class="scroll-section overflow-hidden bg-white py-20 lg:py-28">
        <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:gap-20">
            <div data-reveal="fade-left" class="relative">
                <div data-reveal="zoom-in" data-reveal-delay="200" class="absolute -left-4 -top-4 h-24 w-24 rounded-full bg-brand-muted lg:-left-8 lg:-top-8 lg:h-32 lg:w-32"></div>
                <img
                    src="{{ store_media_url(config('store.images.about_main')) }}"
                    alt="Nông dân TTC"
                    class="relative aspect-[4/5] w-full rounded-2xl object-cover shadow-2xl ring-1 ring-slate-200/50"
                    loading="lazy"
                    decoding="async"
                >
                <img
                    src="{{ store_media_url(config('store.images.about_small')) }}"
                    alt="Thu hoạch"
                    class="absolute -bottom-6 -right-4 hidden w-40 rounded-xl object-cover shadow-xl ring-4 ring-white sm:block lg:-right-8 lg:w-52"
                    loading="lazy"
                    decoding="async"
                >
                <div data-reveal="zoom-in" data-reveal-delay="400" class="absolute -bottom-4 left-4 rounded-xl bg-brand px-4 py-3 text-white shadow-lg lg:-bottom-6 lg:left-8">
                    <p class="text-2xl font-bold">100%</p>
                    <p class="text-xs text-white/85">Nguồn gốc rõ ràng</p>
                </div>
            </div>
            <div data-reveal="fade-right" class="lg:pl-4">
                <span class="mb-3 inline-block rounded-full bg-brand-muted px-4 py-1 text-xs font-semibold uppercase tracking-wider text-brand">Về chúng tôi</span>
                <p class="font-display mb-2 text-lg italic text-brand">Câu chuyện của chúng tôi</p>
                <h2 class="mb-5 text-2xl font-bold text-slate-900 sm:text-4xl">Hành trình nông sản sạch</h2>
                <p class="mb-4 text-slate-600 leading-relaxed">
                    {{ config('store.name') }} ra đời từ niềm tin rằng mỗi gia đình Việt đều xứng đáng được thưởng thức nông sản an toàn, đậm đà hương vị quê nhà.
                </p>
                <p class="mb-8 text-slate-600 leading-relaxed">
                    Hợp tác trực tiếp với hộ nông dân — kiểm soát chất lượng từ thu hoạch đến đóng gói và vận chuyển.
                </p>
                <ul class="mb-8 space-y-3" data-reveal-group>
                    @foreach (['Chứng nhận VSATTP & OCOP', 'Thu hoạch trong 24h', 'Đóng gói chuẩn quốc tế'] as $item)
                        <li data-reveal="fade-left" class="flex items-center gap-3 text-sm text-slate-700">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand/10 text-brand">
                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('about') }}" class="group inline-flex items-center gap-2 rounded-full bg-brand px-6 py-3 text-sm font-semibold uppercase tracking-wide text-white shadow-md transition hover:bg-brand-dark hover:shadow-lg">
                    Tìm hiểu thêm
                    <svg class="h-4 w-4 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Sản phẩm --}}
    <section class="scroll-section bg-gradient-to-b from-brand-muted/50 to-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div data-reveal="fade-up" class="mb-10 flex items-end justify-between">
                <div>
                    <span data-reveal="line" data-reveal-delay="300" class="mb-2 inline-block h-1 w-10 rounded-full bg-harvest"></span>
                    <h2 class="text-2xl font-bold text-slate-900 sm:text-3xl">Top sản phẩm bán chạy</h2>
                    <p class="mt-2 text-sm text-slate-500">Được khách hàng yêu thích nhất tuần này</p>
                </div>
                <a href="{{ route('products.index') }}" class="hidden rounded-full border border-brand/30 px-5 py-2 text-sm font-semibold text-brand transition hover:bg-brand hover:text-white sm:inline-flex sm:items-center sm:gap-1">
                    Xem tất cả
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="product-grid-equal grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-6" data-reveal-group>
                @foreach ($featuredProducts as $product)
                    <div data-reveal="zoom-in" class="h-full">
                        @include('store.partials.product-card', ['product' => $product])
                    </div>
                @endforeach
            </div>

            <div data-reveal="fade-up" data-reveal-delay="200" class="mt-10 text-center sm:hidden">
                <a href="{{ route('products.index') }}" class="inline-block rounded-full bg-brand px-8 py-3 text-sm font-semibold text-white shadow-md hover:bg-brand-dark">
                    Xem tất cả sản phẩm
                </a>
            </div>
        </div>
    </section>

    {{-- Banner CTA --}}
    @if ($banners->isNotEmpty())
        <section class="scroll-section py-16">
            <div class="mx-auto grid max-w-7xl grid-cols-1 gap-5 px-4 sm:px-6 md:grid-cols-2 md:gap-6" data-reveal-group>
                @foreach ($banners as $banner)
                    <a
                        href="{{ $banner->link ?? '#' }}"
                        data-reveal="fade-up"
                        class="group relative block aspect-[2/1] overflow-hidden rounded-2xl shadow-lg ring-1 ring-black/5"
                    >
                        <img src="{{ store_media_url($banner->image) }}" alt="{{ $banner->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" loading="lazy" decoding="async">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/90 via-brand-dark/30 to-transparent transition duration-500 group-hover:from-brand-dark/95"></div>
                        <div class="absolute bottom-0 left-0 p-6 text-white sm:p-8">
                            <h3 class="text-xl font-bold transition group-hover:translate-x-1 sm:text-2xl">{{ $banner->title }}</h3>
                            @if ($banner->subtitle)
                                <p class="mt-2 max-w-sm text-sm text-white/90">{{ $banner->subtitle }}</p>
                            @endif
                            <span class="mt-4 inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-widest text-harvest opacity-0 transition duration-300 group-hover:opacity-100">
                                Khám phá ngay →
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Khuyến mãi --}}
    <section class="scroll-section border-y border-slate-100 bg-white py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div data-reveal="fade-up" class="mb-8 text-center">
                <span class="mb-2 inline-block rounded-full bg-harvest/15 px-3 py-1 text-xs font-bold uppercase tracking-wider text-harvest-dark">Ưu đãi</span>
                <span data-reveal="line" data-reveal-delay="250" class="mx-auto mt-4 block h-0.5 w-16 rounded-full bg-brand/30"></span>
                <h2 class="mt-4 text-2xl font-bold text-slate-900">Khuyến mãi dành cho bạn</h2>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4" data-reveal-group>
                @foreach (config('store.promotions') as $promo)
                    <div data-reveal="fade-up" class="promo-card rounded-xl border border-slate-200 bg-white p-5">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-brand-muted text-brand">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                        <p class="font-semibold text-brand">{{ $promo['title'] }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $promo['desc'] }}</p>
                        <p class="mt-3 rounded bg-slate-50 px-2 py-1 font-mono text-xs text-slate-500">Mã: {{ $promo['code'] }}</p>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $promo['code'] }}'); this.textContent='Đã sao chép!'; setTimeout(() => this.textContent='Sao chép mã', 2000)" class="mt-4 w-full rounded-lg bg-brand py-2.5 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-brand-dark">
                            Sao chép mã
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Tin tức --}}
    <section class="scroll-section bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div data-reveal="fade-up" class="mb-12 text-center">
                <span data-reveal="line" data-reveal-delay="250" class="mx-auto mb-3 block h-1 w-12 rounded-full bg-brand"></span>
                <h2 class="text-2xl font-bold text-slate-900 sm:text-3xl">Tin tức &amp; Bài viết</h2>
                <p class="mt-2 text-sm text-slate-500">Kiến thức nông sản và cập nhật mới nhất</p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3" data-reveal-group>
                @foreach ($posts as $post)
                    <article data-reveal="fade-up" class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 transition hover:-translate-y-1 hover:shadow-xl">
                        <a href="{{ route('posts.show', $post->slug) }}">
                            <div class="aspect-[16/10] overflow-hidden">
                                <img src="{{ store_media_url($post->image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                            </div>
                            <div class="p-6">
                                <time class="text-xs font-medium uppercase tracking-wider text-brand">{{ $post->published_at?->format('d/m/Y') }}</time>
                                <h3 class="mt-2 mb-2 line-clamp-2 text-lg font-bold text-slate-800 transition group-hover:text-brand">{{ $post->title }}</h3>
                                <p class="line-clamp-3 text-sm leading-relaxed text-slate-600">{{ $post->excerpt }}</p>
                                <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand opacity-0 transition group-hover:opacity-100">
                                    Đọc thêm →
                                </span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <div data-reveal="fade-up" data-reveal-delay="300" class="mt-12 text-center">
                <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 rounded-full border-2 border-brand px-8 py-3 text-sm font-semibold text-brand transition hover:bg-brand hover:text-white">
                    Xem tất cả bài viết
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- CTA cuối trang --}}
    <section data-reveal="zoom-in" class="scroll-section relative overflow-hidden bg-brand py-16">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -left-20 -top-20 h-64 w-64 rounded-full bg-white"></div>
            <div class="absolute -bottom-16 -right-16 h-48 w-48 rounded-full bg-harvest"></div>
        </div>
        <div class="relative mx-auto max-w-3xl px-4 text-center text-white sm:px-6">
            <h2 class="text-2xl font-bold sm:text-3xl">Trải nghiệm nông sản sạch ngay hôm nay</h2>
            <p class="mt-3 text-white/85">Đặt hàng online — giao tận nơi — cam kết chất lượng</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('products.index') }}" class="rounded-full bg-white px-8 py-3.5 text-sm font-bold uppercase tracking-wide text-brand shadow-lg transition hover:bg-brand-muted">
                    Mua sắm ngay
                </a>
                <a href="tel:{{ preg_replace('/\s+/', '', config('store.phone')) }}" class="rounded-full border-2 border-white/80 px-8 py-3.5 text-sm font-semibold text-white transition hover:bg-white/10">
                    {{ config('store.phone') }}
                </a>
            </div>
        </div>
    </section>
@endsection
