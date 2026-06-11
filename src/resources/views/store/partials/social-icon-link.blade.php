<a
    href="{{ $href }}"
    target="_blank"
    rel="noopener noreferrer"
    class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full shadow-sm transition hover:opacity-90"
    @if (! empty($label)) aria-label="{{ $label }}" @endif
>
    @include('store.partials.icons.' . $platform)
</a>
