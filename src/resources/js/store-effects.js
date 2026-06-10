document.addEventListener('alpine:init', () => {
    const formatVnd = (amount) => `${Number(amount).toLocaleString('vi-VN')}đ`;

    Alpine.data('cartPage', (initial = {}) => ({
        subtotal: Number(initial.subtotal ?? 0),
        shippingFee: Number(initial.shippingFee ?? 0),
        grandTotal: Number(initial.grandTotal ?? 0),
        freeShipThreshold: 350000,

        get shipRemaining() {
            return Math.max(0, this.freeShipThreshold - this.subtotal);
        },

        get shipProgress() {
            if (this.subtotal >= this.freeShipThreshold) {
                return 100;
            }

            return Math.min(100, (this.subtotal / this.freeShipThreshold) * 100);
        },

        get hasFreeShip() {
            return this.subtotal >= this.freeShipThreshold;
        },

        formatMoney(amount) {
            return formatVnd(amount);
        },

        applyTotals(detail) {
            this.subtotal = Number(detail.subtotal ?? 0);
            this.shippingFee = Number(detail.shipping_fee ?? 0);
            this.grandTotal = Number(detail.grand_total ?? 0);
        },
    }));

    Alpine.data('cartLineItem', (cartKey, unitPrice, initialQty, updateUrl) => ({
        cartKey,
        unitPrice: Number(unitPrice) || 0,
        qty: Number(initialQty) || 1,
        lineTotal: (Number(initialQty) || 1) * (Number(unitPrice) || 0),
        updateUrl,
        busy: false,
        lastSyncedQty: Number(initialQty) || 1,

        formatMoney(amount) {
            return formatVnd(amount);
        },

        async syncQuantity(newQty) {
            if (this.busy) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const body = new FormData();
            body.append('_token', token ?? '');
            body.append('_method', 'PATCH');
            body.append('key', this.cartKey);
            body.append('quantity', String(newQty));

            this.busy = true;

            try {
                const response = await fetch(this.updateUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body,
                    credentials: 'same-origin',
                });

                const data = await response.json().catch(() => ({}));

                if (! response.ok) {
                    throw new Error(data.message ?? 'Không cập nhật được giỏ hàng.');
                }

                if (data.quantity === 0) {
                    window.location.reload();

                    return;
                }

                this.qty = data.quantity;
                this.lineTotal = data.line_total;
                this.lastSyncedQty = data.quantity;
                this.$dispatch('cart-totals-updated', data);
            } catch (error) {
                window.alert(error instanceof Error ? error.message : 'Không cập nhật được giỏ hàng.');
                window.location.reload();
            } finally {
                this.busy = false;
            }
        },

        decrement() {
            const n = parseInt(String(this.qty).replace(/\D/g, ''), 10) || 1;
            if (n <= 1) {
                return;
            }
            this.syncQuantity(n - 1);
        },

        increment() {
            const n = parseInt(String(this.qty).replace(/\D/g, ''), 10) || 0;
            this.syncQuantity(n + 1);
        },

        normalizeQty() {
            const n = parseInt(String(this.qty).replace(/\D/g, ''), 10);
            const next = Number.isNaN(n) || n < 1 ? 1 : n;
            this.qty = next;
            if (next !== this.lastSyncedQty) {
                this.syncQuantity(next);
            }
        },
    }));

    Alpine.data('heroSection', () => ({
        parallax: 0,
        init() {
            const onScroll = () => {
                this.parallax = Math.min(window.scrollY * 0.35, 180);
            };
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });
        },
    }));

    Alpine.data('statCounter', (target, suffix = '') => ({
        current: 0,
        target: Number(target),
        suffix,
        started: false,
        init() {
            const observer = new IntersectionObserver(
                (entries) => {
                    if (entries[0]?.isIntersecting) {
                        this.animate();
                        observer.disconnect();
                    }
                },
                { threshold: 0.5 },
            );
            observer.observe(this.$el);
        },
        animate() {
            if (this.started) {
                return;
            }
            this.started = true;
            const duration = 1800;
            const start = performance.now();
            const step = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 4);
                this.current = Math.floor(this.target * eased);
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    this.current = this.target;
                }
            };
            requestAnimationFrame(step);
        },
    }));

    Alpine.data('scrollTop', () => ({
        visible: false,
        init() {
            const onScroll = () => {
                this.visible = window.scrollY > 500;
            };
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });
        },
        go() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
    }));

    Alpine.data('checkoutForm', (initial = {}) => ({
        step: Number(initial.step ?? 1),
        phoneError: '',
        name: initial.name ?? '',
        phone: initial.phone ?? '',
        email: initial.email ?? '',
        address: initial.address ?? '',
        note: initial.note ?? '',

        goReview() {
            this.phoneError = '';
            const form = this.$refs.checkoutForm;

            if (! form?.reportValidity()) {
                return;
            }

            const digits = this.phone.replace(/\s+/g, '');

            if (! /^0\d{9}$/.test(digits)) {
                this.phoneError = 'Số điện thoại phải có 10 chữ số (Việt Nam).';
                form.querySelector('[name="phone"]')?.focus();

                return;
            }

            this.step = 2;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        backToEdit() {
            this.step = 1;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
    }));

    Alpine.data('floatingContact', () => ({
        visible: false,
        productsReady: false,
        footerInView: false,

        updateVisibility() {
            const hasProductSection = document.getElementById('san-pham');
            const ready = hasProductSection ? this.productsReady : true;
            this.visible = ready && ! this.footerInView;
        },

        init() {
            const productSection = document.getElementById('san-pham');

            if (productSection) {
                const productObserver = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) {
                                this.productsReady = true;
                                return;
                            }

                            this.productsReady = entry.boundingClientRect.top < 0;
                        });
                        this.updateVisibility();
                    },
                    { threshold: 0 },
                );
                productObserver.observe(productSection);
            } else {
                this.productsReady = true;
            }

            const footer = document.getElementById('lien-he');
            if (footer) {
                const footerObserver = new IntersectionObserver(
                    (entries) => {
                        this.footerInView = entries.some((entry) => entry.isIntersecting);
                        this.updateVisibility();
                    },
                    { threshold: 0 },
                );
                footerObserver.observe(footer);
            }

            this.updateVisibility();
        },
    }));
});

function initScrollReveal() {
    if (window.__scrollRevealInit) {
        return;
    }

    const targets = document.querySelectorAll('[data-reveal], [data-reveal-group]');
    if (! targets.length) {
        return;
    }

    window.__scrollRevealInit = true;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-revealed'));
        document.querySelectorAll('[data-reveal-group]').forEach((el) => el.classList.add('is-revealed'));
        return;
    }

    const revealOne = (el) => {
        if (el.classList.contains('is-revealed')) {
            return;
        }
        const delay = Number(el.dataset.revealDelay || 0);
        setTimeout(() => el.classList.add('is-revealed'), delay);
    };

    const revealNested = (root) => {
        root.querySelectorAll('[data-reveal]').forEach((child) => {
            if (child.closest('[data-reveal-group]')) {
                return;
            }
            if (child !== root && root.contains(child)) {
                revealOne(child);
            }
        });
    };

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }
                const el = entry.target;

                if (el.matches('[data-reveal-group]')) {
                    el.classList.add('is-revealed');
                    el.querySelectorAll('[data-reveal]').forEach((child, i) => {
                        if (!child.dataset.revealDelay) {
                            child.dataset.revealDelay = String(i * 90);
                        }
                        revealOne(child);
                    });
                } else {
                    revealOne(el);
                    revealNested(el);
                }

                observer.unobserve(el);
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -50px 0px' },
    );

    document.querySelectorAll('[data-reveal]').forEach((el) => {
        if (el.closest('[data-reveal-group]')) {
            return;
        }
        if (el.parentElement?.closest('[data-reveal]')) {
            return;
        }
        observer.observe(el);
    });

    document.querySelectorAll('[data-reveal-group]').forEach((el) => {
        observer.observe(el);
    });
}

document.addEventListener('DOMContentLoaded', initScrollReveal);
document.addEventListener('alpine:initialized', initScrollReveal);
