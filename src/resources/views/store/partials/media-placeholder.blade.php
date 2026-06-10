@props(['label' => ''])

<div {{ $attributes->merge(['class' => 'flex h-full w-full flex-col items-center justify-center bg-brand-muted text-brand/35']) }} aria-hidden="true">
    <svg class="h-10 w-10 sm:h-12 sm:w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
    </svg>
    @if ($label !== '')
        <span class="mt-2 px-2 text-center text-[10px] font-medium uppercase tracking-wider">{{ $label }}</span>
    @endif
</div>
