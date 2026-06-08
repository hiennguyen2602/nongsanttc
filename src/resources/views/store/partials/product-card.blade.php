<article class="group overflow-hidden rounded-lg border border-slate-200 bg-white transition hover:shadow-md">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <div class="relative aspect-square overflow-hidden bg-slate-100">
            <img
                src="{{ $product->image }}"
                alt="{{ $product->name }}"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                loading="lazy"
            >
            @if ($product->sale_price)
                <span class="absolute left-2 top-2 rounded bg-accent-red px-2 py-0.5 text-xs font-bold text-white">Sale</span>
            @endif
        </div>
        <div class="p-4">
            <h3 class="mb-2 line-clamp-2 min-h-[2.5rem] text-sm font-medium text-slate-800 group-hover:text-brand">{{ $product->name }}</h3>
            <div class="flex items-center justify-between gap-2">
                <div>
                    @if ($product->sale_price)
                        <span class="text-xs text-slate-400 line-through">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                    @endif
                    <p class="text-base font-bold text-slate-900">{{ $product->formattedPrice() }}</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand text-white transition group-hover:bg-brand-dark">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </span>
            </div>
            <span class="mt-2 block text-center text-xs font-semibold uppercase tracking-wide text-brand opacity-0 transition group-hover:opacity-100">
                Thêm vào giỏ
            </span>
        </div>
    </a>
</article>
