@extends('store.layouts.app')

@section('title', 'Sản phẩm — ' . store_setting('name'))
@section('meta_description', 'Danh sách nông sản sạch tại ' . store_setting('name') . '. ' . store_setting('tagline'))
@php
    $listingQuery = array_filter(['category' => $activeCategory]);
    $listingRobots = seo_paginated_robots($products) ?? (request()->filled('q') ? 'noindex,follow' : null);
@endphp
@if ($listingRobots)
    @section('robots', $listingRobots)
@endif
@section('canonical', seo_listing_canonical('products.index', $listingQuery, $products))

@push('json-ld')
    @php
        $productCrumbs = [
            ['name' => 'Trang chủ', 'url' => route('home', absolute: true)],
            ['name' => 'Sản phẩm', 'url' => route('products.index', absolute: true)],
        ];
        if ($activeCategory) {
            $activeCategoryModel = $categories->firstWhere('slug', $activeCategory);
            if ($activeCategoryModel) {
                $productCrumbs[] = [
                    'name' => $activeCategoryModel->name,
                    'url' => route('products.index', ['category' => $activeCategory], absolute: true),
                ];
            }
        }
    @endphp
    @include('partials.seo.breadcrumb', ['crumbs' => $productCrumbs])
@endpush

@section('content')
    <div class="bg-brand py-10 text-white">
        <div class="store-container">
            <h1 class="text-2xl font-bold sm:text-3xl">Sản phẩm</h1>
            <p class="mt-1 text-sm text-white/80">Nông sản sạch — chất lượng từ nông trại</p>
        </div>
    </div>

    <div class="store-container py-10">
        {{-- Category filter --}}
        <div class="mb-8 flex flex-wrap gap-2">
            <a href="{{ route('products.index') }}"
               class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ ! $activeCategory ? 'bg-brand text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Tất cả
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                   class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ $activeCategory === $category->slug ? 'bg-brand text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        @if ($products->isEmpty())
            <div class="py-16 text-center text-slate-500">
                <p>Không tìm thấy sản phẩm phù hợp.</p>
                <a href="{{ route('products.index') }}" class="mt-4 inline-block text-brand hover:underline">Xem tất cả</a>
            </div>
        @else
            <div class="product-grid-equal grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-6">
                @foreach ($products as $product)
                    <div class="h-full">
                        @include('store.partials.product-card', ['product' => $product])
                    </div>
                @endforeach
            </div>
            <div class="mt-10">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
