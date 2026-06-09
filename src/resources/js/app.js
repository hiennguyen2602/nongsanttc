import './bootstrap';
import './store-effects';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('productGallery', (images = []) => ({
        images,
        active: 0,
        lightbox: false,

        get current() {
            return this.images[this.active] ?? {};
        },

        select(index) {
            this.active = index;
        },

        open(index = null) {
            if (index !== null) {
                this.active = index;
            }
            if (this.images.length) {
                this.lightbox = true;
            }
        },

        close() {
            this.lightbox = false;
        },

        next() {
            if (this.images.length) {
                this.active = (this.active + 1) % this.images.length;
            }
        },

        prev() {
            if (this.images.length) {
                this.active = (this.active - 1 + this.images.length) % this.images.length;
            }
        },
    }));

    Alpine.data('productImages', (existing = []) => ({
        existing: existing.map((item) => ({ path: item.path, url: item.url })),
        newImages: [],
        main: existing.length ? 'existing:' + existing[0].path : null,
        nextId: 1,

        addFiles(event) {
            Array.from(event.target.files).forEach((file) => {
                const id = this.nextId++;
                this.newImages.push({ id, file, url: URL.createObjectURL(file) });
                if (! this.main) {
                    this.main = 'new:' + id;
                }
            });
            this.syncFileInput();
        },

        removeExisting(path) {
            this.existing = this.existing.filter((item) => item.path !== path);
            if (this.main === 'existing:' + path) {
                this.resetMain();
            }
        },

        removeNew(id) {
            this.newImages = this.newImages.filter((item) => item.id !== id);
            if (this.main === 'new:' + id) {
                this.resetMain();
            }
            this.syncFileInput();
        },

        resetMain() {
            if (this.existing.length) {
                this.main = 'existing:' + this.existing[0].path;
            } else if (this.newImages.length) {
                this.main = 'new:' + this.newImages[0].id;
            } else {
                this.main = null;
            }
        },

        setMainExisting(path) {
            this.main = 'existing:' + path;
        },

        setMainNew(id) {
            this.main = 'new:' + id;
        },

        isMainExisting(path) {
            return this.main === 'existing:' + path;
        },

        isMainNew(id) {
            return this.main === 'new:' + id;
        },

        syncFileInput() {
            const data = new DataTransfer();
            this.newImages.forEach((item) => data.items.add(item.file));
            this.$refs.fileInput.files = data.files;
        },

        get mainPayload() {
            if (! this.main) {
                return '';
            }
            if (this.main.startsWith('existing:')) {
                return this.main;
            }
            const id = parseInt(this.main.slice(4), 10);
            const index = this.newImages.findIndex((item) => item.id === id);
            return index >= 0 ? 'new:' + index : '';
        },
    }));
});

Alpine.start();

function bootRichEditors() {
    if (! document.querySelector('.rich-editor')) {
        return;
    }

    import('./admin-editor.js').then(({ initRichEditors }) => initRichEditors());
}

function formatVietnameseNumber(digits) {
    if (! digits) {
        return '';
    }

    return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function initNumberInputs() {
    document.querySelectorAll('input.input-number').forEach((input) => {
        if (input.dataset.numberInit) {
            return;
        }
        input.dataset.numberInit = '1';

        input.value = formatVietnameseNumber((input.value || '').replace(/\D/g, ''));

        input.addEventListener('input', () => {
            input.value = formatVietnameseNumber(input.value.replace(/\D/g, ''));
        });

        const form = input.closest('form');
        if (form && ! form.dataset.numberSubmit) {
            form.dataset.numberSubmit = '1';
            form.addEventListener('submit', () => {
                form.querySelectorAll('input.input-number').forEach((el) => {
                    el.value = el.value.replace(/\D/g, '');
                });
            });
        }
    });
}

function bootAdminEnhancements() {
    bootRichEditors();
    initNumberInputs();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootAdminEnhancements);
} else {
    bootAdminEnhancements();
}
