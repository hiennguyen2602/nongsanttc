import { Fancybox } from '@fancyapps/ui';
import '@fancyapps/ui/dist/fancybox/fancybox.css';

const DESKTOP_MEDIA = '(min-width: 1024px)';

const THUMBS_GRID_ICON = `<button data-thumbs-action="toggle" class="f-button fancybox-button fancybox-button--thumbs" title="Thumbnails"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14.59 14.59h3.76v3.76h-3.76v-3.76zm-4.47 0h3.76v3.76h-3.76v-3.76zm-4.47 0h3.76v3.76H5.65v-3.76zm8.94-4.47h3.76v3.76h-3.76v-3.76zm-4.47 0h3.76v3.76h-3.76v-3.76zm-4.47 0h3.76v3.76H5.65v-3.76zm8.94-4.47h3.76v3.76h-3.76V5.65zm-4.47 0h3.76v3.76h-3.76V5.65zm-4.47 0h3.76v3.76H5.65V5.65z"></path></svg></button>`;

const ZOOM_FACTOR = 1.5;

const ZOOM_TOOLBAR_ITEM = {
    tpl: '<button class="f-button" title="Zoom"><svg><g><line x1="11" y1="8" x2="11" y2="14"/></g><circle cx="11" cy="11" r="7.5"/><path d="m21 21-4.35-4.35M8 11h6"/></svg></button>',
    click: (carousel) => toggleActiveSlideZoom(carousel),
};

function isSlideZoomed(panzoom) {
    if (! panzoom) {
        return false;
    }

    const baseScale = panzoom.getStartPosition().scale;

    return panzoom.getTransform().scale > baseScale * 1.01;
}

function toggleZoom(panzoom, srcEvent = null) {
    if (! panzoom) {
        return;
    }

    if (isSlideZoomed(panzoom)) {
        panzoom.execute('reset');

        return;
    }

    const params = { scale: ZOOM_FACTOR };

    if (srcEvent) {
        params.srcEvent = srcEvent;
    }

    panzoom.execute('zoomIn', params);
}

function toggleActiveSlideZoom(carousel) {
    const panzoom = carousel?.getPage()?.slides?.[0]?.panzoomRef;
    toggleZoom(panzoom);
}

function resetActiveSlideZoom(carousel) {
    const slide = carousel?.getPage()?.slides?.[0];
    slide?.panzoomRef?.execute('reset');
    syncActiveSlideZoomState(carousel);
}

function syncPanzoomCursorState(panzoom) {
    const fancyboxEl = panzoom?.getContainer?.()?.closest?.('.product-fancybox');

    if (fancyboxEl) {
        fancyboxEl.classList.toggle('is-slide-zoomed', isSlideZoomed(panzoom));
    }
}

function syncActiveSlideZoomState(carousel) {
    const panzoom = carousel?.getPage()?.slides?.[0]?.panzoomRef;
    syncPanzoomCursorState(panzoom);
}

const productFancyboxOptions = {
    backdropClick: 'close',
    closeButton: false,
    zoomEffect: true,
    fadeEffect: true,
    showClass: 'f-zoomInUp',
    hideClass: 'f-fadeOut',
    mainClass: 'product-fancybox',
    Carousel: {
        Toolbar: {
            absolute: true,
            enabled: true,
            display: {
                left: [],
                right: ['autoplay', ZOOM_TOOLBAR_ITEM, 'thumbs', 'close'],
            },
            items: {
                thumbs: {
                    tpl: THUMBS_GRID_ICON,
                },
            },
        },
        Thumbs: {
            type: 'scrollable',
            showOnStart: false,
            Carousel: {
                classes: {
                    container: 'fancybox__thumbs',
                },
            },
        },
        Autoplay: {
            autoStart: false,
            timeout: 3500,
            pauseOnHover: true,
        },
        Zoomable: {
            Panzoom: {
                maxScale: 4,
                clickAction: false,
                singleClickAction: false,
                wheelAction: false,
                on: {
                    click: (panzoom, event) => {
                        toggleZoom(panzoom, event?.srcEvent);
                    },
                    ready: (panzoom) => syncPanzoomCursorState(panzoom),
                    animationEnd: (panzoom) => syncPanzoomCursorState(panzoom),
                },
            },
        },
    },
};

function galleryThumbElements() {
    const strip = document.querySelector('.product-gallery-thumbs');
    if (! strip) {
        return [];
    }

    return [...strip.querySelectorAll('[data-gallery-thumb] img')];
}

function galleryMainElements() {
    const swipe = document.querySelector('.product-gallery-swipe');
    if (! swipe) {
        return [];
    }

    return [...swipe.querySelectorAll('.product-gallery-slide-img')];
}

export function isProductGalleryDesktop() {
    return window.matchMedia(DESKTOP_MEDIA).matches;
}

export function openProductGallery(images, startIndex = 0, onIndexChange) {
    if (! isProductGalleryDesktop() || ! images.length) {
        return false;
    }

    const thumbEls = galleryThumbElements();
    const mainEls = galleryMainElements();

    const slides = images.map((image, index) => ({
        src: image.full,
        thumbSrc: image.thumb,
        thumbEl: thumbEls[index] || mainEls[index] || undefined,
        type: 'image',
    }));

    Fancybox.show(slides, {
        ...productFancyboxOptions,
        startIndex,
        on: {
            wheel: (fancybox, event, delta) => {
                if (event.defaultPrevented || event.target.closest('.fancybox__thumbs')) {
                    return;
                }

                const carousel = fancybox.getCarousel();
                if (! carousel) {
                    return;
                }

                event.preventDefault();

                // Lăn lên (delta < 0) → next, lăn xuống (delta > 0) → prev
                if (delta < 0) {
                    carousel.next();
                } else if (delta > 0) {
                    carousel.prev();
                }
            },
            ready: (fancybox) => {
                syncActiveSlideZoomState(fancybox.getCarousel());
            },
            'Carousel.change': (_fancybox, carousel, _pageIndex, prevPageIndex) => {
                if (prevPageIndex !== undefined) {
                    resetActiveSlideZoom(carousel);
                } else {
                    syncActiveSlideZoomState(carousel);
                }

                if (typeof onIndexChange === 'function') {
                    onIndexChange(carousel.getPageIndex());
                }
            },
        },
    });

    return true;
}
