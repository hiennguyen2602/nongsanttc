@extends('store.layouts.app')

@php
    $productShareUrl = route('products.show', $product->slug, absolute: true);
    $productOgImage = store_media_url($product->image, 'large');
@endphp

@section('title', $product->name . ' — ' . store_setting('name'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description ?: store_setting('tagline')), 200))

@push('head')
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="{{ store_setting('name') }}">
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="">
    <meta property="og:url" content="{{ $productShareUrl }}">
    @if ($productOgImage)
        <meta property="og:image" content="{{ $productOgImage }}">
        <meta property="og:image:secure_url" content="{{ preg_replace('/^http:/', 'https:', $productOgImage) }}">
    @endif
    <meta property="og:price:amount" content="{{ $product->displayPrice() }}">
    <meta property="og:price:currency" content="VND">
@endpush

@section('content')
    <div class="store-container py-8 lg:py-12">
        {{-- Breadcrumb --}}
        <nav class="mb-6 text-sm text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-brand">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="{{ route('products.index') }}" class="hover:text-brand">Sản phẩm</a>
            <span class="mx-2">/</span>
            <span class="text-slate-800">{{ $product->name }}</span>
        </nav>

        <div
            class="grid grid-cols-1 gap-10 lg:grid-cols-12"
            x-data="{
                quantity: 1,
                selectedVariant: @js($product->variants->first()?->id),
                variants: @js($product->variants->map(fn($v) => ['id' => $v->id, 'label' => $v->label(), 'price' => $v->price, 'formatted' => $v->formattedPrice()])),
                basePrice: {{ $product->displayPrice() }},
                gallery: @js(store_media_gallery_items($product->image, (array) ($product->gallery ?? []))),
                activeIndex: 0,
                lightbox: false,
                openLightbox() {
                    this.lightbox = true;
                    document.documentElement.classList.add('overflow-hidden');
                },
                closeLightbox() {
                    this.lightbox = false;
                    document.documentElement.classList.remove('overflow-hidden');
                },
                next() { if (this.gallery.length) this.activeIndex = (this.activeIndex + 1) % this.gallery.length; },
                prev() { if (this.gallery.length) this.activeIndex = (this.activeIndex - 1 + this.gallery.length) % this.gallery.length; },
                clampQuantity() {
                    const n = parseInt(String(this.quantity).replace(/\D/g, ''), 10);
                    this.quantity = Number.isNaN(n) || n < 1 ? 1 : n;
                },
                decrementQuantity() {
                    const n = parseInt(String(this.quantity).replace(/\D/g, ''), 10) || 1;
                    this.quantity = Math.max(1, n - 1);
                },
                incrementQuantity() {
                    const n = parseInt(String(this.quantity).replace(/\D/g, ''), 10) || 0;
                    this.quantity = n + 1;
                }
            }"
        >
            {{-- Gallery --}}
            <div class="lg:col-span-5">
                <div class="relative mb-4 aspect-square overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                    <div class="flex h-full transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]" :style="`transform: translateX(-${activeIndex * 100}%)`">
                        <template x-for="(img, i) in gallery" :key="i">
                            <div class="group/main h-full w-full shrink-0 cursor-zoom-in" @click="openLightbox()">
                                <img
                                    :src="img.display"
                                    :srcset="img.srcset"
                                    sizes="(min-width: 1024px) 480px, calc(100vw - 2rem)"
                                    alt="{{ $product->name }}"
                                    class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover/main:scale-[1.03]"
                                    :loading="i === 0 ? 'eager' : 'lazy'"
                                    :fetchpriority="i === 0 ? 'high' : 'auto'"
                                    decoding="async"
                                >
                            </div>
                        </template>
                    </div>
                    <button type="button" x-show="gallery.length > 1" @click.stop="prev()" class="absolute left-2 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-xl text-slate-700 shadow transition hover:bg-white">&lsaquo;</button>
                    <button type="button" x-show="gallery.length > 1" @click.stop="next()" class="absolute right-2 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-xl text-slate-700 shadow transition hover:bg-white">&rsaquo;</button>
                </div>
                <div class="flex gap-2 overflow-x-auto pb-2">
                    <template x-for="(img, i) in gallery" :key="i">
                        <button type="button" @click="activeIndex = i" class="h-16 w-16 shrink-0 overflow-hidden rounded border-2 transition hover:border-brand" :class="activeIndex === i ? 'border-brand' : 'border-slate-200'">
                            <img
                                :src="img.thumb"
                                :srcset="img.srcset"
                                sizes="64px"
                                alt=""
                                class="h-full w-full object-cover"
                                loading="lazy"
                                decoding="async"
                            >
                        </button>
                    </template>
                </div>
            </div>

            {{-- Lightbox --}}
            <template x-teleport="body">
                <div
                    x-show="lightbox"
                    x-cloak
                    @keydown.escape.window="closeLightbox()"
                    class="fixed inset-0 z-[100]"
                    role="dialog"
                    aria-modal="true"
                >
                    <div
                        class="product-lightbox-backdrop absolute inset-0"
                        x-show="lightbox"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        @click="closeLightbox()"
                    ></div>

                    <div class="relative flex h-full items-center justify-center p-4 sm:p-8" @click.self="closeLightbox()">
                        <button type="button" @click="closeLightbox()" class="absolute right-4 top-4 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-2xl text-white transition hover:bg-white/20">&times;</button>
                        <button type="button" @click.stop="prev()" x-show="gallery.length > 1" class="absolute left-2 top-1/2 z-10 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-3xl text-white transition hover:bg-white/20 sm:left-4">&lsaquo;</button>

                        <div
                            x-show="lightbox"
                            x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-500"
                            x-transition:enter-start="opacity-0 scale-[0.82]"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-250"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-[0.94]"
                            class="product-lightbox-image relative z-[1] flex max-h-[90vh] max-w-[92vw] items-center justify-center"
                            @click.stop
                        >
                            <img
                                :src="lightbox ? gallery[activeIndex]?.full : null"
                                :srcset="lightbox ? gallery[activeIndex]?.fullSrcset : null"
                                sizes="92vw"
                                :key="activeIndex"
                                alt="{{ $product->name }}"
                                x-show="lightbox"
                                x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="max-h-[90vh] max-w-[92vw] object-contain"
                                loading="lazy"
                                decoding="async"
                            >
                        </div>

                        <button type="button" @click.stop="next()" x-show="gallery.length > 1" class="absolute right-2 top-1/2 z-10 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-3xl text-white transition hover:bg-white/20 sm:right-4">&rsaquo;</button>
                    </div>
                </div>
            </template>

            {{-- Product info --}}
            <div class="lg:col-span-4">
                <h1 class="text-xl font-bold text-slate-900 sm:text-2xl">{{ $product->name }}</h1>
                <div class="mt-2 flex flex-wrap gap-3 text-sm text-slate-500">
                    @if ($product->sku)
                        <span>Mã: <strong class="text-slate-700">{{ $product->sku }}</strong></span>
                    @endif
                    <span class="text-brand">Còn hàng</span>
                </div>

                <p class="mt-4 text-3xl font-bold text-accent-red" x-text="variants.length && selectedVariant ? variants.find(v => v.id === selectedVariant)?.formatted : '{{ $product->formattedPrice() }}'">
                    {{ $product->formattedPrice() }}
                </p>

                @if ($product->variants->isNotEmpty())
                    <div class="mt-6 space-y-4">
                        @php
                            $flavors = $product->variants->pluck('flavor')->filter()->unique();
                            $sizes = $product->variants->pluck('size')->filter()->unique();
                        @endphp
                        @if ($flavors->isNotEmpty())
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Vị</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($flavors as $flavor)
                                        <span class="rounded border border-slate-300 px-3 py-1.5 text-sm">{{ $flavor }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if ($sizes->isNotEmpty())
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Size / Quy cách</label>
                                <select x-model.number="selectedVariant" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:ring-brand">
                                    @foreach ($product->variants as $variant)
                                        <option value="{{ $variant->id }}">{{ $variant->label() }} — {{ $variant->formattedPrice() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mt-6">
                    <label class="mb-2 block text-sm font-medium text-slate-700">Số lượng</label>
                    <div class="inline-flex items-center rounded-lg border border-slate-300">
                        <button type="button" @click="decrementQuantity()" class="px-4 py-2 text-lg hover:bg-slate-50">−</button>
                        <input
                            type="text"
                            inputmode="numeric"
                            autocomplete="off"
                            x-model="quantity"
                            @blur="clampQuantity()"
                            @keydown.enter.prevent="$event.target.blur()"
                            class="qty-input w-24 min-w-[6rem] border-x border-slate-300 px-2 py-2 text-center text-sm font-medium focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
                            aria-label="Số lượng"
                        >
                        <button type="button" @click="incrementQuantity()" class="px-4 py-2 text-lg hover:bg-slate-50">+</button>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <form method="POST" action="{{ route('cart.add') }}" class="flex-1" @submit="clampQuantity()">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="variant_id" :value="selectedVariant">
                        <input type="hidden" name="quantity" :value="quantity">
                        <button type="submit" class="w-full rounded border-2 border-accent-red py-3 text-sm font-bold uppercase text-accent-red transition hover:bg-accent-red hover:text-white">
                            Thêm vào giỏ
                        </button>
                    </form>
                    <form method="POST" action="{{ route('cart.add') }}" class="flex-1" @submit="clampQuantity()">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="variant_id" :value="selectedVariant">
                        <input type="hidden" name="quantity" :value="quantity">
                        <input type="hidden" name="buy_now" value="1">
                        <button type="submit" class="w-full rounded bg-accent-red py-3 text-sm font-bold uppercase text-white transition hover:bg-red-700">
                            Mua ngay
                        </button>
                    </form>
                </div>

                <div
                    class="mt-6 flex items-center gap-3"
                    x-data="{
                        copied: false,
                        copyUrl() {
                            navigator.clipboard.writeText(@js($productShareUrl)).then(() => {
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            });
                        },
                        openFacebookShare(event) {
                            const shareUrl = @js($productShareUrl);
                            const encoded = encodeURIComponent(shareUrl);
                            const webShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encoded;
                            const ua = navigator.userAgent;

                            if (! /iPhone|iPad|iPod|Android/i.test(ua)) {
                                return;
                            }

                            event.preventDefault();

                            if (/Android/i.test(ua)) {
                                const fallback = encodeURIComponent(webShareUrl);
                                window.location.href = 'intent://www.facebook.com/sharer/sharer.php?u=' + encoded
                                    + '#Intent;package=com.facebook.katana;scheme=https;S.browser_fallback_url=' + fallback + ';end';
                                return;
                            }

                            let appOpened = false;
                            const onHide = () => { appOpened = true; };
                            document.addEventListener('visibilitychange', onHide, { once: true });
                            window.location.href = 'fb://share?link=' + encoded;
                            setTimeout(() => {
                                document.removeEventListener('visibilitychange', onHide);
                                if (! appOpened) {
                                    window.open(webShareUrl, '_blank', 'noopener,noreferrer');
                                }
                            }, 1200);
                        }
                    }"
                >
                    <span class="shrink-0 text-sm font-semibold text-slate-700">Chia sẻ:</span>
                    <div class="flex items-center gap-2">
                        @if (store_setting('facebook'))
                        <a
                            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($productShareUrl) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            @click="openFacebookShare($event)"
                            class="flex h-9 w-9 shrink-0 cursor-pointer items-center justify-center rounded-full bg-[#1877F2] text-white transition hover:bg-[#166fe5]"
                            aria-label="Chia sẻ lên Facebook"
                        >
                            <svg class="h-[18px] w-[18px]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        @endif
                        @if (store_setting('messenger'))
                        <a
                            href="{{ store_setting('messenger') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-9 w-9 shrink-0 cursor-pointer items-center justify-center rounded-full bg-[#0084FF] text-white transition hover:bg-[#0073e6]"
                            aria-label="Chia sẻ qua Messenger"
                        >
                            <svg class="h-[18px] w-[18px]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0C5.373 0 0 4.975 0 11.111c0 3.497 1.745 6.616 4.472 8.652V24l4.086-2.242c1.09.3 2.246.464 3.442.464 6.627 0 12-4.974 12-11.111C24 4.975 18.627 0 12 0zm1.191 14.963l-3.055-3.26-5.963 3.26L10.732 8.8l3.13 3.26 5.89-3.26-3.561 6.163z"/></svg>
                        </a>
                        @endif
                        <div class="relative">
                            <button
                                type="button"
                                @click="copyUrl()"
                                class="flex h-9 w-9 shrink-0 cursor-pointer items-center justify-center rounded-full bg-sky-500 text-white transition hover:bg-sky-600"
                                aria-label="Sao chép liên kết"
                            >
                                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </button>
                            <span
                                x-show="copied"
                                x-transition
                                x-cloak
                                class="pointer-events-none absolute -top-9 left-1/2 z-10 -translate-x-1/2 whitespace-nowrap rounded bg-slate-800 px-2 py-1 text-xs text-white shadow"
                            >Đã sao chép</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cam kết — sidebar --}}
            <div class="lg:col-span-3">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                    <h2 class="mb-4 font-bold text-brand">{{ store_setting('name') }} Cam kết</h2>
                    <ul class="space-y-3">
                        @foreach (config('store.commitments') as $item)
                            <li class="flex items-start gap-3 text-sm text-slate-700">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand/10 text-brand">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </span>
                                {{ $item['text'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Banner đại lý --}}
        <div class="mt-12 overflow-hidden rounded-xl bg-brand">
            <div class="flex flex-col items-center justify-between gap-4 px-6 py-8 sm:flex-row sm:px-10">
                <div class="text-center text-white sm:text-left">
                    <p class="text-lg font-bold">Trở thành đại lý — Lan tỏa nông sản sạch</p>
                    <p class="mt-1 text-sm text-white/80">Đăng ký ngay để nhận chính sách hỗ trợ tốt nhất</p>
                </div>
                <a href="{{ route('contact') }}" class="shrink-0 rounded bg-white px-6 py-2.5 text-sm font-bold uppercase text-brand hover:bg-slate-100">
                    Đăng ký ngay
                </a>
            </div>
        </div>

        {{-- Khuyến mãi --}}
        <div class="mt-12">
            <h2 class="mb-6 text-lg font-bold text-slate-900">Khuyến mãi dành cho bạn</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($promotions as $promo)
                    <div class="rounded-lg border border-slate-200 p-4">
                        <p class="font-semibold text-brand">{{ $promo->title }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $promo->description }}</p>
                        <p class="mt-2 text-xs text-slate-400">Mã: {{ $promo->code }}</p>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $promo->code }}')" class="mt-3 w-full rounded bg-brand py-2 text-xs font-semibold uppercase text-white hover:bg-brand-dark">
                            Sao chép mã
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Mô tả --}}
        @if ($product->description)
            <div class="mt-12 border-t border-slate-200 pt-10">
                <h2 class="mb-4 text-lg font-bold text-slate-900">Mô tả sản phẩm</h2>
                <div class="prose prose-slate max-w-none text-slate-600">
                    {!! $product->description !!}
                </div>
            </div>
        @endif

        {{-- Sản phẩm liên quan --}}
        @if ($relatedProducts->isNotEmpty())
            <div class="mt-12 border-t border-slate-200 pt-10">
                <h2 class="mb-6 text-lg font-bold text-slate-900">Sản phẩm liên quan</h2>
                <div class="product-grid-equal grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($relatedProducts as $related)
                        <div class="h-full">
                            @include('store.partials.product-card', ['product' => $related])
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Sản phẩm đã xem --}}
        @if ($viewedProducts->isNotEmpty())
            <div class="mt-12 border-t border-slate-200 pt-10">
                <h2 class="mb-6 text-lg font-bold text-slate-900">Sản phẩm đã xem</h2>
                <div class="product-grid-equal grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($viewedProducts as $viewed)
                        <div class="h-full">
                            @include('store.partials.product-card', ['product' => $viewed])
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
