@php
    $buttonClass = match ($platform) {
        'facebook' => 'overflow-hidden rounded-full shadow-sm transition hover:opacity-90',
        'messenger' => 'rounded-full bg-[#0084FF] text-white transition hover:bg-[#0073e6]',
        'zalo' => 'overflow-hidden rounded-full shadow-sm transition hover:opacity-90',
        default => 'rounded-full bg-slate-500 text-white transition hover:bg-slate-600',
    };
@endphp

<a
    href="{{ $href }}"
    target="_blank"
    rel="noopener noreferrer"
    class="flex h-9 w-9 shrink-0 items-center justify-center {{ $buttonClass }}"
    @if (! empty($label)) aria-label="{{ $label }}" @endif
>
    @include('store.partials.icons.' . $platform)
</a>
