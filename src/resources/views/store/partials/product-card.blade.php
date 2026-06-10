@php
    $hasSale = $product->sale_price && $product->sale_price < $product->price;
@endphp

<article class="product-card-hover group flex h-full flex-col overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm">
    <a href="{{ route('products.show', $product->slug) }}" class="flex h-full flex-col">
        <div class="relative aspect-square shrink-0 overflow-hidden bg-brand-muted">
            @if ($productImageUrl = store_media_url($product->image, 'medium'))
                <img
                    src="{{ $productImageUrl }}"
                    alt="{{ $product->name }}"
                    class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                    loading="lazy"
                >
            @else
                @include('store.partials.media-placeholder')
            @endif
            @if ($hasSale)
                <span class="absolute left-3 top-3 rounded-full bg-accent-red px-2.5 py-0.5 text-xs font-bold text-white shadow-sm">Sale</span>
            @endif
            <div class="absolute inset-x-0 bottom-0 translate-y-full bg-gradient-to-t from-brand-dark/90 to-transparent p-4 transition duration-300 group-hover:translate-y-0">
                <span class="text-xs font-semibold uppercase tracking-wide text-white">Xem chi tiết →</span>
            </div>
        </div>

        <div class="product-card-body flex flex-col p-4">
            <h3 class="mb-1 line-clamp-2 min-h-[2.35rem] text-sm font-medium leading-snug text-slate-800 transition group-hover:text-brand">
                {{ $product->name }}
            </h3>

            <div class="flex items-end justify-between gap-2">
                <div class="min-w-0">
                    <span @class([
                        'block min-h-4 text-xs leading-4',
                        'text-slate-400 line-through' => $hasSale,
                        'invisible' => ! $hasSale,
                    ])>
                        @if ($hasSale)
                            {{ number_format($product->price, 0, ',', '.') }}đ
                        @else
                            &nbsp;
                        @endif
                    </span>
                    <p class="text-base font-bold leading-tight text-slate-900">{{ $product->formattedPrice() }}</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand text-white shadow-md transition group-hover:scale-110 group-hover:bg-brand-dark">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </span>
            </div>
        </div>
    </a>
</article>
