import { createGallerySwipeMethods } from '../gallery-swipe';

document.addEventListener('alpine:init', () => {
    Alpine.data('storeProductDetail', (config) => ({
        quantity: 1,
        selectedVariant: config.selectedVariant,
        variants: config.variants,
        basePrice: config.basePrice,
        gallery: config.gallery,
        activeIndex: 0,
        lightbox: false,

        ...createGallerySwipeMethods(function itemCount() {
            return this.gallery.length;
        }),

        openLightbox() {
            if (this.galleryDidSwipe) {
                return;
            }

            this.lightbox = true;
            document.documentElement.classList.add('overflow-hidden');
        },

        closeLightbox() {
            this.lightbox = false;
            document.documentElement.classList.remove('overflow-hidden');
        },

        next() {
            if (this.gallery.length) {
                this.activeIndex = (this.activeIndex + 1) % this.gallery.length;
            }
        },

        prev() {
            if (this.gallery.length) {
                this.activeIndex = (this.activeIndex - 1 + this.gallery.length) % this.gallery.length;
            }
        },

        clampQuantity() {
            const parsed = parseInt(String(this.quantity).replace(/\D/g, ''), 10);
            this.quantity = Number.isNaN(parsed) || parsed < 1 ? 1 : parsed;
        },

        decrementQuantity() {
            const parsed = parseInt(String(this.quantity).replace(/\D/g, ''), 10) || 1;
            this.quantity = Math.max(1, parsed - 1);
        },

        incrementQuantity() {
            const parsed = parseInt(String(this.quantity).replace(/\D/g, ''), 10) || 0;
            this.quantity = parsed + 1;
        },
    }));
});
