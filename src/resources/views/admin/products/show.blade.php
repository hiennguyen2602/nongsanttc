@extends('admin.layouts.app')

@section('title', $product->name)
@section('page-title', 'Chi tiết sản phẩm')

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
            <h2>{{ $product->name }}</h2>
            <div class="form-actions" style="margin:0">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary btn-sm">Sửa</a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
            </div>
        </div>
        <div class="x_content">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2" x-data="productGallery(@js($galleryImages))">
                {{-- Gallery --}}
                <div>
                    @if (count($galleryImages))
                        <div class="relative aspect-square w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                            <div class="flex h-full transition-transform duration-300 ease-out" :style="`transform: translateX(-${active * 100}%)`">
                                <template x-for="(img, i) in images" :key="i">
                                    <div class="h-full w-full shrink-0 cursor-zoom-in" @click="open(i)">
                                        <img :src="img.display" alt="{{ $product->name }}" class="h-full w-full object-contain">
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

                {{-- Thông tin --}}
                <div>
                    <dl class="product-detail-list">
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

                {{-- Lightbox --}}
                <template x-teleport="body">
                    <div x-show="lightbox" x-cloak x-transition.opacity @keydown.escape.window="close()" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 p-4" @click.self="close()">
                        <button type="button" @click="close()" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-2xl text-white hover:bg-white/20">&times;</button>
                        <button type="button" @click="prev()" x-show="images.length > 1" class="absolute left-4 top-1/2 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-3xl text-white hover:bg-white/20">&lsaquo;</button>
                        <img :src="current.full" alt="" x-show="lightbox" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" class="max-h-[90vh] max-w-[92vw] object-contain">
                        <button type="button" @click="next()" x-show="images.length > 1" class="absolute right-4 top-1/2 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-3xl text-white hover:bg-white/20">&rsaquo;</button>
                    </div>
                </template>
            </div>

            @if ($product->variants->isNotEmpty())
                <div class="mt-8">
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

            @if ($product->description)
                <div class="mt-8 border-t border-slate-200 pt-6">
                    <h3 class="panel-title mb-2">Mô tả chi tiết</h3>
                    <div class="prose max-w-none text-slate-700">{!! $product->description !!}</div>
                </div>
            @endif
        </div>
    </div>
@endsection
