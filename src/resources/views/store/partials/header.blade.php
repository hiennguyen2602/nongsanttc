@php
    $heroDesktopUrl = request()->routeIs('home') ? store_media_url(store_setting('hero_desktop')) : null;
    // Chỉ home + có ảnh hero mới header trong suốt lúc đầu; about và home không ảnh luôn nền brand
    $headerTransparent = request()->routeIs('home') && $heroDesktopUrl;

    $navLinkClass = function (bool $active) use ($headerTransparent): string {
        $base = 'border-b px-3 py-2 text-xs font-medium uppercase tracking-widest transition sm:px-4 sm:text-sm ';

        if ($headerTransparent) {
            return $base . ($active ? 'border-white text-white' : 'border-transparent text-white/85 hover:text-white');
        }

        return $base . ($active ? 'border-brand-light text-white' : 'border-transparent text-white/75 hover:text-white');
    };

    $contactNavClass = $headerTransparent
        ? 'border-b border-transparent px-3 py-2 text-xs font-medium uppercase tracking-widest text-white/85 transition hover:text-white sm:px-4 sm:text-sm'
        : 'border-b px-3 py-2 text-xs font-medium uppercase tracking-widest transition sm:px-4 sm:text-sm ' . (request()->routeIs('contact') ? 'border-brand-light text-white' : 'border-transparent text-white/90 hover:text-white');
@endphp

<header
    x-data="{
        mobileOpen: false,
        searchOpen: false,
        scrolled: false,
        transparent: @js($headerTransparent)
    }"
    x-init="
        const onScroll = () => { scrolled = window.scrollY > 50 };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    "
    class="site-header fixed inset-x-0 top-0 z-50 text-white transition-all duration-300{{ $headerTransparent ? '' : ' bg-brand/95 shadow-md backdrop-blur-sm' }}"
    @if ($headerTransparent)
    :class="scrolled ? 'bg-brand/95 shadow-md backdrop-blur-sm' : 'bg-transparent shadow-none'"
    @endif
>
    <div class="store-container">
        <div class="flex h-16 items-center justify-between lg:h-20">
            <a href="{{ route('home') }}" class="flex shrink-0 flex-col leading-tight">
                <span class="font-display text-lg font-semibold tracking-wide text-white transition-colors sm:text-xl">
                    {{ store_setting('name') }}
                </span>
                <span class="text-[10px] font-medium uppercase tracking-[0.25em] text-white/70 transition-colors sm:text-xs">
                    Nông sản sạch
                </span>
            </a>

            <nav class="hidden items-center gap-0.5 lg:flex">
                <a href="{{ route('home') }}"
                   class="{{ $navLinkClass(request()->routeIs('home')) }}"
                   :class="transparent && !scrolled
                       ? '{{ request()->routeIs('home') ? 'border-white text-white' : 'border-transparent text-white/85 hover:text-white' }}'
                       : '{{ request()->routeIs('home') ? 'border-brand-light text-white' : 'border-transparent text-white/75 hover:text-white' }}'">
                    Trang chủ
                </a>
                <a href="{{ route('about') }}"
                   class="{{ $navLinkClass(request()->routeIs('about')) }}"
                   :class="transparent && !scrolled
                       ? '{{ request()->routeIs('about') ? 'border-white text-white' : 'border-transparent text-white/85 hover:text-white' }}'
                       : '{{ request()->routeIs('about') ? 'border-brand-light text-white' : 'border-transparent text-white/75 hover:text-white' }}'">
                    Về chúng tôi
                </a>
                <a href="{{ route('products.index') }}"
                   class="{{ $navLinkClass(request()->routeIs('products.*')) }}"
                   :class="transparent && !scrolled
                       ? '{{ request()->routeIs('products.*') ? 'border-white text-white' : 'border-transparent text-white/85 hover:text-white' }}'
                       : '{{ request()->routeIs('products.*') ? 'border-brand-light text-white' : 'border-transparent text-white/75 hover:text-white' }}'">
                    Sản phẩm
                </a>
                <a href="{{ route('posts.index') }}"
                   class="{{ $navLinkClass(request()->routeIs('posts.*')) }}"
                   :class="transparent && !scrolled
                       ? '{{ request()->routeIs('posts.*') ? 'border-white text-white' : 'border-transparent text-white/85 hover:text-white' }}'
                       : '{{ request()->routeIs('posts.*') ? 'border-brand-light text-white' : 'border-transparent text-white/90 hover:text-white' }}'">
                    Tin tức
                </a>
                <a href="{{ route('contact') }}"
                   class="{{ $contactNavClass }}"
                   :class="transparent && !scrolled ? 'text-white/85 hover:text-white' : '{{ request()->routeIs('contact') ? 'border-brand-light text-white' : 'text-white/90 hover:text-white' }}'">
                    Liên hệ
                </a>
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                <a
                    href="tel:{{ preg_replace('/\s+/', '', store_setting('phone')) }}"
                    class="hidden items-center border px-3 py-1.5 text-xs text-white transition lg:inline-flex {{ $headerTransparent ? 'border-white/60 hover:bg-white/10' : 'border-white/30 hover:border-brand-light' }}"
                    :class="transparent && !scrolled
                        ? 'border-white/60 text-white hover:bg-white/10'
                        : 'border-white/30 text-white hover:border-brand-light'"
                >
                    Hotline: {{ store_setting('phone') }}
                </a>

                <button type="button" @click="searchOpen = !searchOpen" class="rounded p-2 text-white/90 hover:text-white" aria-label="Tìm kiếm">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="relative cursor-pointer rounded p-2 text-white/90 hover:text-white" aria-label="Giỏ hàng" onclick="window.location='{{ route('cart.index') }}'">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    @if ($cartCount > 0)
                        <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-accent-red text-[10px] font-bold text-white">{{ $cartCount }}</span>
                    @endif
                </button>
                <button type="button" @click="mobileOpen = !mobileOpen" class="rounded p-2 text-white lg:hidden" aria-label="Menu">
                    <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div
            x-show="searchOpen"
            x-transition
            class="border-t border-white/10 pb-4 pt-3"
            style="display:none"
        >
            <form action="{{ route('products.index') }}" method="GET" class="mx-auto flex w-full max-w-xl gap-2">
                <input type="search" name="q" placeholder="Tìm sản phẩm..." class="min-w-0 flex-1 rounded border-0 bg-white px-4 py-2 text-sm text-slate-800 shadow-sm focus:ring-2 focus:ring-white/80">
                <button type="submit" class="shrink-0 rounded bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-600">Tìm</button>
            </form>
        </div>
    </div>

    <div
        x-show="mobileOpen"
        x-transition
        class="border-t bg-brand-dark lg:hidden"
        style="display:none"
    >
        <nav class="store-container space-y-1 py-4">
            <a href="{{ route('home') }}" class="block px-3 py-2.5 text-sm uppercase tracking-wide text-white/90 hover:text-white">Trang chủ</a>
            <a href="{{ route('about') }}" class="block px-3 py-2.5 text-sm uppercase tracking-wide text-white/90 hover:text-white">Về chúng tôi</a>
            <a href="{{ route('products.index') }}" class="block px-3 py-2.5 text-sm uppercase tracking-wide text-white/90 hover:text-white">Sản phẩm</a>
            <a href="{{ route('posts.index') }}" class="block px-3 py-2.5 text-sm uppercase tracking-wide text-white/90 hover:text-white">Tin tức</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2.5 text-sm uppercase tracking-wide text-white/90 hover:text-white">Liên hệ</a>
            <a href="tel:{{ preg_replace('/\s+/', '', store_setting('phone')) }}" class="mt-2 block border border-white/30 px-3 py-2.5 text-center text-sm text-white">
                {{ store_setting('phone') }}
            </a>
        </nav>
    </div>
</header>

@if (! $headerTransparent)
    <div class="h-16 lg:h-20" aria-hidden="true"></div>
@endif
