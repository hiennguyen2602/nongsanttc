@extends('admin.layouts.app')

@section('title', 'Thông tin sản phẩm')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Danh sách sản phẩm', 'url' => route('admin.products.index')],
        ['label' => 'Thông tin sản phẩm'],
    ])])
@endsection

@php
    $galleryImages = collect(array_merge([$product->image], $product->gallery ?? []))
        ->filter()
        ->unique()
        ->map(fn ($img) => [
            'thumb' => store_media_url($img, 'thumbnail'),
            'display' => store_media_url($img, 'medium'),
            'full' => store_media_url($img, 'large'),
        ])
        ->values()
        ->all();
@endphp

@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>Thông tin sản phẩm</h2>
            <div class="form-actions" style="margin:0">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary btn-sm">Sửa</a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
            </div>
        </div>
        <div class="x_content">
            <div class="product-detail-layout flex flex-col gap-8 lg:flex-row lg:items-start" x-data="productGallery(@js($galleryImages))">
                {{-- Trái: ảnh --}}
                <div class="product-gallery-viewport w-full shrink-0">
                    @if (count($galleryImages))
                        <div class="relative aspect-square w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                            <div
                                class="product-gallery-swipe flex h-full transition-transform duration-300 ease-out"
                                :style="galleryTrackStyle()"
                                @pointerdown="onGalleryPointerDown($event)"
                                @pointerup="onGalleryPointerUp($event)"
                                @pointercancel="onGalleryPointerCancel($event)"
                            >
                                <template x-for="(img, i) in images" :key="i">
                                    <div class="h-full shrink-0 cursor-zoom-in" :style="gallerySlideStyle()" @click="open(i)" @dragstart.prevent>
                                        <img :src="img.display" alt="{{ $product->name }}" class="product-gallery-slide-img h-full w-full object-cover" draggable="false">
                                    </div>
                                </template>
                            </div>
                            <button type="button" x-show="images.length > 1" @click.stop="prev()" class="absolute left-2 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-xl text-slate-700 shadow hover:bg-white">&lsaquo;</button>
                            <button type="button" x-show="images.length > 1" @click.stop="next()" class="absolute right-2 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-xl text-slate-700 shadow hover:bg-white">&rsaquo;</button>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <template x-for="(img, i) in images" :key="i">
                                <button type="button" @click="select(i)" class="h-16 w-16 overflow-hidden rounded-lg border-2 transition" :class="active === i ? 'border-sky-600' : 'border-slate-200 hover:border-slate-400'">
                                    <img :src="img.thumb" alt="" class="h-full w-full object-cover">
                                </button>
                            </template>
                        </div>
                    @else
                        <div class="flex aspect-square w-full items-center justify-center rounded-xl bg-slate-100 text-slate-400 ring-1 ring-slate-200">Chưa có ảnh</div>
                    @endif
                </div>

                {{-- Phải: thông tin + biến thể --}}
                <div class="product-detail-side min-w-0 flex flex-1 flex-col gap-6">
                    <div class="product-detail-info">
                        <dl class="product-detail-list">
                            <div><dt>Tên sản phẩm</dt><dd>{{ $product->name }}</dd></div>
                            <div><dt>Mã sản phẩm</dt><dd>{{ $product->sku ?? '—' }}</dd></div>
                            <div><dt>Danh mục</dt><dd>{{ $product->category?->name ?? '—' }}</dd></div>
                            <div>
                                <dt>Giá</dt>
                                <dd>
                                    @if ($product->sale_price !== null && $product->price !== null && $product->sale_price < $product->price)
                                        <span class="text-lg font-bold text-rose-600">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                                        <span class="ml-2 text-sm text-slate-400 line-through">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                    @else
                                        <span class="text-lg font-bold text-slate-800">{{ $product->formattedPrice() }}</span>
                                    @endif
                                </dd>
                            </div>
                            <div><dt>Tồn kho</dt><dd>{{ $product->formattedStock() }}</dd></div>
                            <div>
                                <dt>Trạng thái</dt>
                                <dd>
                                    @include('admin.partials.status-badge', ['label' => $product->visibilityLabel(), 'class' => $product->visibilityBadgeClass()])
                                    @if ($product->is_featured)<span class="badge badge-warning">Nổi bật</span>@endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    @if ($product->variants->isNotEmpty())
                        <div class="product-detail-variants min-w-0">
                            <h3 class="panel-title mb-2">Biến thể</h3>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead><tr><th class="cell-text">Hương vị</th><th class="cell-text">Kích cỡ</th><th class="cell-price">Giá</th><th class="cell-num">Tồn kho</th></tr></thead>
                                    <tbody>
                                        @foreach ($product->variants as $variant)
                                            <tr>
                                                <td class="cell-text">{{ $variant->flavor ?? '—' }}</td>
                                                <td class="cell-text">{{ $variant->size ?? '—' }}</td>
                                                <td class="cell-price">{{ $variant->formattedPrice() }}</td>
                                                <td class="cell-num">{{ $variant->formattedStock() }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Lightbox --}}
                <template x-teleport="body">
                    <div
                        x-show="lightbox"
                        x-cloak
                        @keydown.escape.window="close()"
                        @keydown.arrow-left.window="lightbox && prev()"
                        @keydown.arrow-right.window="lightbox && next()"
                        class="fixed inset-0 z-[100]"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div
                            class="product-lightbox-backdrop absolute inset-0"
                            x-show="lightbox"
                            x-transition.opacity
                            @click="close()"
                        ></div>

                        <div class="relative flex h-full items-center justify-center p-4 sm:p-8" @click.self="close()">
                            <button type="button" @click="close()" class="absolute right-4 top-4 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-2xl text-white hover:bg-white/20">&times;</button>
                            <button type="button" @click.stop="prev()" x-show="images.length > 1" class="absolute left-2 top-1/2 z-10 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-3xl text-white hover:bg-white/20 sm:left-4">&lsaquo;</button>

                            <div
                                class="product-lightbox-swipe relative z-[1] max-h-[90vh] w-full max-w-[92vw] overflow-hidden"
                                @pointerdown="onGalleryPointerDown($event)"
                                @pointerup="onGalleryPointerUp($event)"
                                @pointercancel="onGalleryPointerCancel($event)"
                            >
                                <div
                                    x-show="lightbox"
                                    class="flex h-full max-h-[90vh] transition-transform duration-300 ease-out"
                                    :style="galleryTrackStyle()"
                                >
                                    <template x-for="(img, i) in images" :key="'lb-' + i">
                                        <div class="flex h-full max-h-[90vh] shrink-0 items-center justify-center" :style="gallerySlideStyle()" @dragstart.prevent>
                                            <img
                                                :src="img.full"
                                                alt="{{ $product->name }}"
                                                class="product-gallery-slide-img max-h-[90vh] max-w-full object-contain"
                                                draggable="false"
                                            >
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <button type="button" @click.stop="next()" x-show="images.length > 1" class="absolute right-2 top-1/2 z-10 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-3xl text-white hover:bg-white/20 sm:right-4">&rsaquo;</button>
                        </div>
                    </div>
                </template>
            </div>

            @if ($product->description)
                <div class="mt-8 border-t border-slate-200 pt-6">
                    <h3 class="panel-title mb-2">Mô tả chi tiết</h3>
                    <div class="prose max-w-none text-slate-700">{!! $product->description !!}</div>
                </div>
            @endif
        </div>
    </div>
@endsection
