@extends('store.layouts.app')

@php
    $productShareUrl = route('products.show', $product->slug, absolute: true);
    $productOgImage = store_media_url($product->image, 'large');
    $productTitle = filled($product->meta_title) ? trim($product->meta_title) : $product->name;
    $productDescription = seo_entity_description($product->description, $product->meta_description);
    $productSchemaImages = seo_product_schema_images($product);
@endphp

@section('title', seo_entity_title($product->name, $product->meta_title))
@section('meta_description', $productDescription)
@section('canonical', $productShareUrl)
@section('og_type', 'product')
@section('og_title', $productTitle)
@section('og_description', $productDescription)
@section('og_url', $productShareUrl)
@section('og_image_alt', $productTitle)
@if ($productOgImage)
    @section('og_image', $productOgImage)
@endif

@push('head')
    @if ($product->displayPrice() !== null)
        <meta property="og:price:amount" content="{{ $product->displayPrice() }}">
        <meta property="og:price:currency" content="VND">
    @endif
@endpush

@push('json-ld')
    @include('partials.seo.json-ld', ['data' => [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Trang chủ', 'item' => route('home', absolute: true)],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Sản phẩm', 'item' => route('products.index', absolute: true)],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $product->name, 'item' => $productShareUrl],
        ],
    ]])
    @php
        $productSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $productDescription,
            'sku' => $product->sku ?: null,
            'url' => $productShareUrl,
            'image' => $productSchemaImages,
            'brand' => ['@type' => 'Brand', 'name' => store_setting('name')],
        ];
        if ($product->displayPrice() !== null) {
            $productSchema['offers'] = [
                '@type' => 'Offer',
                'url' => $productShareUrl,
                'priceCurrency' => 'VND',
                'price' => $product->displayPrice(),
                'availability' => seo_product_in_stock($product)
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ];
        }
    @endphp
    @include('partials.seo.json-ld', ['data' => array_filter($productSchema, fn ($value) => $value !== null && $value !== '')])
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
            x-data="storeProductDetail({
                selectedVariant: @js($product->variants->first()?->id),
                variants: @js($product->variants->map(fn($v) => ['id' => $v->id, 'label' => $v->label(), 'price' => $v->price, 'formatted' => $v->formattedPrice()])),
                basePrice: @js($product->displayPrice()),
                gallery: @js(store_media_gallery_items($product->image, (array) ($product->gallery ?? []))),
            })"
        >
            {{-- Gallery --}}
            <div class="lg:col-span-5">
                <div class="product-gallery-main relative mb-4 aspect-square overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                    <div
                        class="product-gallery-swipe flex h-full transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"
                        :style="galleryTrackStyle()"
                        @pointerdown="onGalleryPointerDown($event)"
                        @pointerup="onGalleryPointerUp($event)"
                        @pointercancel="onGalleryPointerCancel($event)"
                    >
                        <template x-for="(img, i) in gallery" :key="i">
                            <div class="group/main h-full shrink-0 lg:cursor-zoom-in" :style="gallerySlideStyle()" @click.stop="openLightbox()" @dragstart.prevent>
                                <img
                                    :src="img.display"
                                    :srcset="img.srcset"
                                    sizes="(min-width: 1024px) 480px, calc(100vw - 2rem)"
                                    alt="{{ $product->name }}"
                                    class="product-gallery-slide-img h-full w-full object-cover transition-transform duration-500 ease-out group-hover/main:scale-[1.03]"
                                    draggable="false"
                                    :loading="i === 0 ? 'eager' : 'lazy'"
                                    :fetchpriority="i === 0 ? 'high' : 'auto'"
                                    decoding="async"
                                >
                            </div>
                        </template>
                    </div>
                    <button type="button" x-show="gallery.length > 1" @click.stop="prev()" class="product-gallery-nav absolute left-2 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-slate-700 shadow transition hover:bg-white" aria-label="Ảnh trước">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" x-show="gallery.length > 1" @click.stop="next()" class="product-gallery-nav absolute right-2 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-slate-700 shadow transition hover:bg-white" aria-label="Ảnh sau">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <div
                    class="product-gallery-thumbs-carousel relative"
                    x-show.flex="gallery.length > 1"
                    x-cloak
                >
                    <button
                        type="button"
                        class="product-gallery-thumbs-nav absolute left-0 top-1/2 z-10 flex h-8 w-8 -translate-y-1/2 items-center justify-center"
                        aria-label="Ảnh trước"
                        @click.stop="scrollThumbsPrev()"
                    >
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="product-gallery-thumbs" x-ref="thumbStrip">
                        <template x-for="(img, i) in gallery" :key="i">
                            <button type="button" data-gallery-thumb @click.stop="activeIndex = i" class="h-16 w-16 shrink-0 overflow-hidden rounded border-2 transition hover:border-brand" :class="activeIndex === i ? 'border-brand' : 'border-slate-200'">
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
                    <button
                        type="button"
                        class="product-gallery-thumbs-nav absolute right-0 top-1/2 z-10 flex h-8 w-8 -translate-y-1/2 items-center justify-center"
                        aria-label="Ảnh sau"
                        @click.stop="scrollThumbsNext()"
                    >
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

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

                @include('store.partials.share-buttons', [
                    'shareUrl' => $productShareUrl,
                    'shareTitle' => $product->name,
                ])
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
                @include('partials.rich-content', ['html' => $product->description])
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
