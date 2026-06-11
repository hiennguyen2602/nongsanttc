@php
    $contactUrl = store_setting('messenger') ?: store_setting('zalo');
    $hideOnCartCheckout = request()->routeIs('cart.*', 'checkout.*');
@endphp

@if (! $hideOnCartCheckout)
<div
    x-data="floatingContact()"
    x-show="visible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="pointer-events-none fixed bottom-4 right-4 z-40 flex flex-col items-end gap-2 sm:bottom-8 sm:right-6"
    style="display: none;"
>
    @if (store_setting('phone'))
    <a href="tel:{{ preg_replace('/\s+/', '', store_setting('phone')) }}"
       class="pointer-events-auto flex h-11 w-11 items-center justify-center rounded-full bg-green-600 text-white shadow-lg transition hover:scale-105 hover:bg-green-700"
       title="Gọi {{ store_setting('phone') }}"
       aria-label="Gọi {{ store_setting('phone') }}">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
        </svg>
    </a>
    @endif
    @if (store_setting('zalo'))
    <a href="{{ store_setting('zalo') }}" target="_blank" rel="noopener"
       class="pointer-events-auto flex h-11 w-11 items-center justify-center overflow-hidden rounded-full shadow-lg transition hover:scale-105"
       title="Zalo">
        @include('store.partials.icons.zalo')
    </a>
    @endif
    @if (store_setting('messenger'))
    <a href="{{ store_setting('messenger') }}" target="_blank" rel="noopener"
       class="pointer-events-auto flex h-11 w-11 items-center justify-center rounded-full bg-[#0084FF] text-white shadow-lg transition hover:scale-105"
       title="Messenger">
        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 0C5.373 0 0 4.975 0 11.111c0 3.497 1.745 6.616 4.472 8.652V24l4.086-2.242c1.09.3 2.246.464 3.442.464 6.627 0 12-4.974 12-11.111C24 4.975 18.627 0 12 0zm1.191 14.963l-3.055-3.26-5.963 3.26L10.732 8.8l3.13 3.26 5.89-3.26-3.561 6.163z"/>
        </svg>
    </a>
    @endif
    <div class="pointer-events-auto flex max-w-[calc(100vw-2rem)] items-center gap-2 sm:max-w-none">
        @if ($contactUrl)
        <a href="{{ $contactUrl }}" target="_blank" rel="noopener"
           class="flex min-w-0 items-center gap-2 rounded-full bg-brand px-4 py-2.5 text-sm font-medium text-white shadow-lg transition hover:bg-brand-dark">
            <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 0C5.373 0 0 4.975 0 11.111c0 3.497 1.745 6.616 4.472 8.652V24l4.086-2.242c1.09.3 2.246.464 3.442.464 6.627 0 12-4.974 12-11.111C24 4.975 18.627 0 12 0zm1.191 14.963l-3.055-3.26-5.963 3.26L10.732 8.8l3.13 3.26 5.89-3.26-3.561 6.163z"/>
            </svg>
            <span class="truncate">Liên hệ {{ store_setting('name') }}</span>
        </a>
        @endif
        @if (store_setting('google_maps_url'))
        <a href="{{ store_setting('google_maps_url') }}" target="_blank" rel="noopener"
           class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand text-white shadow-lg transition hover:scale-105"
           title="Bản đồ">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </a>
        @endif
    </div>
</div>
@endif
