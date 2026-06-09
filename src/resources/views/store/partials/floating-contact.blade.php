<div class="fixed bottom-24 right-4 z-40 flex flex-col gap-2 sm:bottom-8 sm:right-6">
    <a href="tel:{{ preg_replace('/\s+/', '', store_setting('phone')) }}"
       class="flex h-11 w-11 items-center justify-center rounded-full bg-accent-red text-white shadow-lg transition hover:scale-105"
       title="Gọi điện">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
    </a>
    @if (store_setting('zalo'))
    <a href="{{ store_setting('zalo') }}" target="_blank" rel="noopener"
       class="flex h-11 w-11 items-center justify-center rounded-full bg-blue-500 text-xs font-bold text-white shadow-lg transition hover:scale-105"
       title="Zalo">Zalo</a>
    @endif
    @if (store_setting('google_maps_url'))
    <a href="{{ store_setting('google_maps_url') }}" target="_blank" rel="noopener"
       class="flex h-11 w-11 items-center justify-center rounded-full bg-brand text-white shadow-lg transition hover:scale-105"
       title="Bản đồ">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </a>
    @endif
    @if (store_setting('messenger'))
    <a href="{{ store_setting('messenger') }}" target="_blank" rel="noopener"
       class="flex h-11 w-11 items-center justify-center rounded-full bg-purple-500 text-white shadow-lg transition hover:scale-105"
       title="Messenger">
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.145 2 11.243c0 2.891 1.435 5.462 3.678 7.125L4 22l4.06-1.352C9.18 21.445 10.564 21.7 12 21.7c5.523 0 10-4.145 10-9.243S17.523 2 12 2z"/></svg>
    </a>
    @endif
</div>

@if (store_setting('zalo'))
<a href="{{ store_setting('zalo') }}" target="_blank" rel="noopener"
   class="fixed bottom-4 right-4 z-40 flex items-center gap-2 rounded-full bg-brand px-4 py-2.5 text-sm font-medium text-white shadow-lg transition hover:bg-brand-dark sm:bottom-8">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    Liên hệ {{ store_setting('name') }}
</a>
@endif
