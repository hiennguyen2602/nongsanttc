@php
    $wrapperClass = $wrapperClass ?? 'mt-6';
@endphp

<div
    class="{{ $wrapperClass }} flex items-center gap-3"
    x-data="{
        copied: false,
        copyUrl() {
            navigator.clipboard.writeText(@js($shareUrl)).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            });
        },
        openFacebookShare(event) {
            const shareUrl = @js($shareUrl);
            const shareTitle = @js($shareTitle);
            const encoded = encodeURIComponent(shareUrl);
            const webShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encoded;
            const facewebHref = encodeURIComponent(webShareUrl);
            const ua = navigator.userAgent;
            const isMobile = /iPhone|iPad|iPod|Android/i.test(ua);

            if (! isMobile) {
                return;
            }

            event.preventDefault();

            let appOpened = false;
            const onHide = () => { appOpened = true; };
            document.addEventListener('visibilitychange', onHide, { once: true });

            if (/Android/i.test(ua)) {
                window.location.href = 'intent://facewebmodal/f?href=' + facewebHref
                    + '#Intent;scheme=fb;package=com.facebook.katana;S.browser_fallback_url=' + encodeURIComponent(webShareUrl) + ';end';
            } else {
                window.location.href = 'fb://facewebmodal/f?href=' + facewebHref;
            }

            setTimeout(() => {
                document.removeEventListener('visibilitychange', onHide);
                if (appOpened) {
                    return;
                }

                if (navigator.share) {
                    navigator.share({ title: shareTitle, url: shareUrl }).catch(() => {
                        window.location.href = webShareUrl;
                    });
                    return;
                }

                window.location.href = webShareUrl;
            }, 1500);
        }
    }"
>
    <span class="shrink-0 text-sm font-semibold text-slate-700">Chia sẻ:</span>
    <div class="flex items-center gap-2">
        @if (store_setting('facebook'))
        <a
            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
            target="_blank"
            rel="noopener noreferrer"
            @click="openFacebookShare($event)"
            class="flex h-9 w-9 shrink-0 cursor-pointer items-center justify-center rounded-full bg-[#1877F2] text-white transition hover:bg-[#166fe5]"
            aria-label="Chia sẻ lên Facebook"
        >
            @include('store.partials.icons.facebook-share')
        </a>
        @endif
        @if (store_setting('messenger'))
        <a
            href="{{ store_setting('messenger') }}"
            target="_blank"
            rel="noopener noreferrer"
            class="flex h-9 w-9 shrink-0 cursor-pointer items-center justify-center overflow-hidden rounded-full transition hover:opacity-90"
            aria-label="Chia sẻ qua Messenger"
        >
            @include('store.partials.icons.messenger')
        </a>
        @endif
        <div class="relative">
            <button
                type="button"
                @click="copyUrl()"
                class="flex h-9 w-9 shrink-0 cursor-pointer items-center justify-center rounded-full bg-sky-500 text-white transition hover:bg-sky-600"
                aria-label="Sao chép liên kết"
            >
                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
            </button>
            <span
                x-show="copied"
                x-transition
                x-cloak
                class="pointer-events-none absolute -top-9 left-1/2 z-10 -translate-x-1/2 whitespace-nowrap rounded bg-slate-800 px-2 py-1 text-xs text-white shadow"
            >Đã sao chép</span>
        </div>
    </div>
</div>
