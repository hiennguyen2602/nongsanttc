@extends('store.layouts.app')

@section('title', config('store.name') . ' — ' . config('store.tagline'))

@section('content')
    {{-- Mục 1: Hero — The Venue style --}}
    <section class="relative flex min-h-[70vh] items-center justify-center overflow-hidden lg:min-h-[85vh]">
        <img
            src="https://images.unsplash.com/photo-1625246333195-78d9c38ad449?w=1920&h=1080&fit=crop"
            alt="Nông sản TTC"
            class="absolute inset-0 h-full w-full object-cover"
        >
        <div class="absolute inset-0 bg-black/55"></div>
        <div class="relative z-10 mx-auto max-w-4xl px-4 text-center text-white">
            <p class="font-display mb-3 text-lg italic text-amber-200/90 sm:text-xl">{{ config('store.name') }}</p>
            <h1 class="mb-4 text-3xl font-bold leading-tight sm:text-5xl lg:text-6xl">
                Trải nghiệm nông sản<br>sạch & chân thực
            </h1>
            <p class="mx-auto mb-8 max-w-2xl text-sm text-white/85 sm:text-base">
                {{ config('store.tagline') }}. Từ thửa ruộng xanh đến bàn ăn gia đình — chất lượng minh bạch, nguồn gốc rõ ràng.
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('products.index') }}" class="rounded bg-brand px-6 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-brand-light">
                    Khám phá sản phẩm
                </a>
                <a href="{{ route('about') }}" class="rounded border border-white/60 px-6 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-white/10">
                    Câu chuyện thương hiệu
                </a>
            </div>
        </div>
    </section>

    {{-- Mục 2: Giới thiệu — The Venue split layout --}}
    <section class="bg-white py-16 lg:py-24">
        <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:gap-16">
            <div class="relative">
                <img
                    src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=800&h=900&fit=crop"
                    alt="Nông dân TTC"
                    class="aspect-[4/5] w-full rounded-lg object-cover shadow-xl"
                >
                <img
                    src="https://images.unsplash.com/photo-1574323347407-f5e1e8c4df8d?w=400&h=500&fit=crop"
                    alt="Thu hoạch"
                    class="absolute -bottom-6 -right-4 hidden w-40 rounded-lg object-cover shadow-lg ring-4 ring-white sm:block lg:w-48"
                >
            </div>
            <div class="lg:-ml-8 lg:bg-white lg:p-10 lg:shadow-xl">
                <p class="font-display mb-2 text-brand italic">Câu chuyện của chúng tôi</p>
                <h2 class="mb-4 text-2xl font-bold text-slate-900 sm:text-3xl">Hành trình nông sản sạch</h2>
                <p class="mb-4 text-slate-600 leading-relaxed">
                    {{ config('store.name') }} ra đời từ niềm tin rằng mỗi gia đình Việt đều xứng đáng được thưởng thức nông sản an toàn, đậm đà hương vị quê nhà. Chúng tôi hợp tác trực tiếp với hộ nông dân, kiểm soát chất lượng từ khâu thu hoạch đến đóng gói.
                </p>
                <p class="mb-6 text-slate-600 leading-relaxed">
                    Sản phẩm đa dạng từ gạo, rau củ, đặc sản vùng miền đến set quà tặng cao cấp — phù hợp cho mọi nhu cầu sử dụng và biếu tặng.
                </p>
                <a href="{{ route('about') }}" class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-brand hover:text-brand-dark">
                    Tìm hiểu thêm
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Mục 3: Sản phẩm — comchayxua carousel/grid --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-brand sm:text-3xl">Top sản phẩm bán chạy</h2>
                    <p class="mt-1 text-sm text-slate-500">Nông sản được khách hàng tin dùng nhất</p>
                </div>
                <a href="{{ route('products.index') }}" class="hidden text-sm font-semibold text-brand hover:text-brand-dark sm:inline-flex sm:items-center sm:gap-1">
                    Xem tất cả
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-6">
                @foreach ($featuredProducts as $product)
                    @include('store.partials.product-card', ['product' => $product])
                @endforeach
            </div>

            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('products.index') }}" class="inline-block rounded bg-brand px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-dark">
                    Xem tất cả sản phẩm
                </a>
            </div>
        </div>
    </section>

    {{-- Mục 4: Banner đôi CTA --}}
    @if ($banners->isNotEmpty())
        <section class="py-12">
            <div class="mx-auto grid max-w-7xl grid-cols-1 gap-4 px-4 sm:px-6 md:grid-cols-2 md:gap-6">
                @foreach ($banners as $banner)
                    <a href="{{ $banner->link ?? '#' }}" class="group relative block aspect-[2/1] overflow-hidden rounded-xl shadow-md">
                        <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-6 text-white">
                            <h3 class="text-lg font-bold sm:text-xl">{{ $banner->title }}</h3>
                            @if ($banner->subtitle)
                                <p class="mt-1 text-sm text-white/85">{{ $banner->subtitle }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Khuyến mãi --}}
    <section class="border-y border-slate-200 bg-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <h2 class="mb-6 text-xl font-bold text-slate-900">Khuyến mãi dành cho bạn</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (config('store.promotions') as $promo)
                    <div class="rounded-lg border border-slate-200 p-4">
                        <p class="font-semibold text-brand">{{ $promo['title'] }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $promo['desc'] }}</p>
                        <p class="mt-2 text-xs text-slate-400">Mã: {{ $promo['code'] }}</p>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $promo['code'] }}')" class="mt-3 w-full rounded bg-brand py-2 text-xs font-semibold uppercase text-white hover:bg-brand-dark">
                            Sao chép mã
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Mục 5: Tin tức — Ogani 3 cột --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="mb-10 text-center">
                <span class="mb-2 inline-block h-1 w-12 bg-brand"></span>
                <h2 class="text-2xl font-bold text-slate-900 sm:text-3xl">Tin tức &amp; Bài viết</h2>
                <p class="mt-2 text-sm text-slate-500">Cập nhật kiến thức nông sản và ưu đãi mới nhất</p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                @foreach ($posts as $post)
                    <article class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200 transition hover:shadow-md">
                        <a href="{{ route('posts.show', $post->slug) }}">
                            <div class="aspect-[16/10] overflow-hidden">
                                <img src="{{ $post->image }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition hover:scale-105" loading="lazy">
                            </div>
                            <div class="p-5">
                                <div class="mb-2 flex items-center gap-3 text-xs text-slate-400">
                                    <span>{{ $post->published_at?->format('d/m/Y') }}</span>
                                </div>
                                <h3 class="mb-2 line-clamp-2 font-bold text-slate-800 hover:text-brand">{{ $post->title }}</h3>
                                <p class="line-clamp-3 text-sm text-slate-600">{{ $post->excerpt }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand hover:text-brand-dark">
                    Xem tất cả bài viết
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>
@endsection
