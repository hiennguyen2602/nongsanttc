<form method="POST" action="{{ route('cart.remove', $item['key']) }}">
    @csrf @method('DELETE')
    @if (($style ?? 'text') === 'icon')
        <button
            type="submit"
            class="flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition hover:bg-red-50 hover:text-red-600"
            title="Xóa sản phẩm"
            aria-label="Xóa {{ $item['name'] }}"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    @else
        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 hover:underline">
            Xóa
        </button>
    @endif
</form>
