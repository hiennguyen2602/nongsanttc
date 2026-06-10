@extends('store.layouts.app')

@section('title', 'Sản phẩm — ' . config('store.name'))

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
                {{ $products->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
