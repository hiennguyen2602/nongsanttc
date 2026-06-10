document.addEventListener('alpine:init', () => {
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

    Alpine.data('cartLineItem', (initialQty = 1) => ({
        qty: Number(initialQty) || 1,

        submitQty() {
            this.$refs.qtyForm?.requestSubmit();
        },

        decrement() {
            if (this.qty <= 1) {
                return;
            }
            this.qty--;
            this.submitQty();
        },

        increment() {
            this.qty++;
            this.submitQty();
        },

        normalizeQty() {
            const n = parseInt(String(this.qty).replace(/\D/g, ''), 10);
            this.qty = Number.isNaN(n) || n < 1 ? 1 : n;
            this.submitQty();
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
