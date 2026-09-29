<div
    x-data="scrollTop()"
    x-show="visible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed bottom-8 left-4 z-40 sm:bottom-8"
    style="display: none;"
>
    <button
        type="button"
        @click="go()"
        class="flex h-11 w-11 items-center justify-center rounded-full bg-brand text-white shadow-lg ring-4 ring-white/80 transition hover:bg-brand-dark hover:scale-105"
        aria-label="Lên đầu trang"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
    </button>
</div>
