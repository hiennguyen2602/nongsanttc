<form x-ref="qtyForm" method="POST" action="{{ route('cart.update') }}">
    @csrf @method('PATCH')
    <input type="hidden" name="key" value="{{ $item['key'] }}">
    <div class="inline-flex items-stretch overflow-hidden rounded-lg border border-slate-300 bg-white shadow-sm">
        <button
            type="button"
            @click="decrement()"
            class="flex h-10 w-10 items-center justify-center text-lg text-slate-600 transition hover:bg-slate-50 hover:text-brand"
            aria-label="Giảm số lượng"
        >−</button>
        <input
            type="text"
            inputmode="numeric"
            name="quantity"
            x-model="qty"
            @blur="normalizeQty()"
            @keydown.enter.prevent="$event.target.blur()"
            class="h-10 w-14 border-x border-slate-300 text-center text-sm font-semibold text-slate-900 focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand sm:w-16"
            aria-label="Số lượng"
        >
        <button
            type="button"
            @click="increment()"
            class="flex h-10 w-10 items-center justify-center text-lg text-slate-600 transition hover:bg-slate-50 hover:text-brand"
            aria-label="Tăng số lượng"
        >+</button>
    </div>
</form>
