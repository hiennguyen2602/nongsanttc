@extends('store.layouts.app')

@section('title', $product->name . ' — ' . config('store.name'))

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
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
                gallery: @js(collect(array_merge([$product->image], $product->gallery ?? []))->filter()->unique()->map(fn ($img) => ['thumb' => store_media_url($img, 'thumbnail'), 'display' => store_media_url($img, 'medium'), 'full' => store_media_url($img, 'large')])->values()->all()),
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
                prev() { if (this.gallery.length) this.activeIndex = (this.activeIndex - 1 + this.gallery.length) % this.gallery.length; }
            }"
        >
            {{-- Gallery --}}
            <div class="lg:col-span-5">
                <div class="relative mb-4 aspect-square overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                    <div class="flex h-full transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]" :style="`transform: translateX(-${activeIndex * 100}%)`">
                        <template x-for="(img, i) in gallery" :key="i">
                            <div class="group/main h-full w-full shrink-0 cursor-zoom-in" @click="openLightbox()">
                                <img :src="img.display" alt="{{ $product->name }}" class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover/main:scale-[1.03]">
                            </div>
                        </template>
                    </div>
                    <button type="button" x-show="gallery.length > 1" @click.stop="prev()" class="absolute left-2 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-xl text-slate-700 shadow transition hover:bg-white">&lsaquo;</button>
                    <button type="button" x-show="gallery.length > 1" @click.stop="next()" class="absolute right-2 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-xl text-slate-700 shadow transition hover:bg-white">&rsaquo;</button>
                </div>
                <div class="flex gap-2 overflow-x-auto pb-2">
                    <template x-for="(img, i) in gallery" :key="i">
                        <button type="button" @click="activeIndex = i" class="h-16 w-16 shrink-0 overflow-hidden rounded border-2 transition hover:border-brand" :class="activeIndex === i ? 'border-brand' : 'border-slate-200'">
                            <img :src="img.thumb" alt="" class="h-full w-full object-cover">
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
                                :src="gallery[activeIndex]?.full"
                                :key="activeIndex"
                                alt="{{ $product->name }}"
                                x-show="lightbox"
                                x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="max-h-[90vh] max-w-[92vw] object-contain"
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
                        <button type="button" @click="quantity = Math.max(1, quantity - 1)" class="px-4 py-2 text-lg hover:bg-slate-50">−</button>
                        <span class="min-w-[3rem] text-center font-medium" x-text="quantity"></span>
                        <button type="button" @click="quantity++" class="px-4 py-2 text-lg hover:bg-slate-50">+</button>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <form method="POST" action="{{ route('cart.add') }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="variant_id" :value="selectedVariant">
                        <input type="hidden" name="quantity" :value="quantity">
                        <button type="submit" class="w-full rounded border-2 border-accent-red py-3 text-sm font-bold uppercase text-accent-red transition hover:bg-accent-red hover:text-white">
                            Thêm vào giỏ
                        </button>
                    </form>
                    <form method="POST" action="{{ route('cart.add') }}" class="flex-1">
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

                <div class="mt-6 flex gap-2">
                    <a href="{{ config('store.facebook') }}" target="_blank" class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600 hover:bg-brand hover:text-white">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="{{ config('store.messenger') }}" target="_blank" class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600 hover:bg-brand hover:text-white">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.145 2 11.243c0 2.891 1.435 5.462 3.678 7.125L4 22l4.06-1.352C9.18 21.445 10.564 21.7 12 21.7c5.523 0 10-4.145 10-9.243S17.523 2 12 2z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Cam kết — sidebar --}}
            <div class="lg:col-span-3">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                    <h2 class="mb-4 font-bold text-brand">{{ config('store.name') }} Cam kết</h2>
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
