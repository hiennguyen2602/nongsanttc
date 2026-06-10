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

    Alpine.data('productVariants', (initial = [], variantErrors = {}) => ({
        variantErrors,

        variants: initial.length
            ? initial.map((v) => ({
                id: v.id ?? '',
                flavor: v.flavor ?? '',
                size: v.size ?? '',
                price: v.price != null ? String(v.price) : '',
                stock: v.stock != null ? String(v.stock) : '0',
            }))
            : [{ id: '', flavor: '', size: '', price: '', stock: '0' }],

        errorFor(index) {
            return this.variantErrors[index] ?? '';
        },

        add() {
            this.variants.push({ id: '', flavor: '', size: '', price: '', stock: '0' });
        },

        remove(index) {
            this.variants.splice(index, 1);
        },

        format(value) {
            const digits = String(value ?? '').replace(/\D/g, '');
            return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },

        setDigits(index, field, value) {
            this.variants[index][field] = value.replace(/\D/g, '');
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

    Alpine.data('featuredImage', (existing = null) => ({
        existing: existing ? { path: existing.path, url: existing.url } : null,
        newImage: null,

        addFile(event) {
            const file = event.target.files?.[0];
            if (! file) {
                return;
            }

            if (this.newImage?.url) {
                URL.revokeObjectURL(this.newImage.url);
            }

            this.newImage = { file, url: URL.createObjectURL(file) };
            this.existing = null;
            this.syncFileInput();
        },

        removeExisting() {
            this.existing = null;
        },

        removeNew() {
            if (this.newImage?.url) {
                URL.revokeObjectURL(this.newImage.url);
            }
            this.newImage = null;
            this.syncFileInput();
        },

        syncFileInput() {
            const data = new DataTransfer();
            if (this.newImage) {
                data.items.add(this.newImage.file);
            }
            this.$refs.fileInput.files = data.files;
        },
    }));
});
