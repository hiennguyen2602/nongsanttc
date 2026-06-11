const SWIPE_THRESHOLD = 36;

export function galleryTrackStyle(activeIndex, count) {
    const total = Math.max(Number(count) || 0, 1);
    const index = Math.min(Math.max(Number(activeIndex) || 0, 0), total - 1);

    return {
        width: `${total * 100}%`,
        transform: `translateX(-${(index * 100) / total}%)`,
    };
}

export function gallerySlideStyle(count) {
    const total = Math.max(Number(count) || 0, 1);

    return {
        width: `${100 / total}%`,
        flex: `0 0 ${100 / total}%`,
    };
}

export function createGallerySwipeMethods(getItemCount) {
    return {
        swipeStartX: 0,
        swipeStartY: 0,
        swipeActive: false,
        swipePointerId: null,
        swipeResolved: false,
        galleryDidSwipe: false,
        _swipePointerMoveHandler: null,
        _swipePointerEndHandler: null,

        galleryTrackStyle() {
            const count = getItemCount.call(this);
            const index = this.activeIndex ?? this.active ?? 0;

            return galleryTrackStyle(index, count);
        },

        gallerySlideStyle() {
            return gallerySlideStyle(getItemCount.call(this));
        },

        isGalleryControlTarget(target) {
            return target instanceof Element && Boolean(target.closest('button, a, [role="button"]'));
        },

        bindSwipePointerHandlers() {
            if (! this._swipePointerMoveHandler) {
                this._swipePointerMoveHandler = (event) => {
                    if (! this.swipeActive || event.pointerId !== this.swipePointerId) {
                        return;
                    }

                    this.evaluateSwipe(event.clientX, event.clientY);
                };

                window.addEventListener('pointermove', this._swipePointerMoveHandler);
            }

            if (! this._swipePointerEndHandler) {
                this._swipePointerEndHandler = (event) => {
                    if (! this.swipeActive || event.pointerId !== this.swipePointerId) {
                        return;
                    }

                    this.evaluateSwipe(event.clientX, event.clientY);
                    this.endSwipe();
                };

                window.addEventListener('pointerup', this._swipePointerEndHandler);
                window.addEventListener('pointercancel', this._swipePointerEndHandler);
            }
        },

        unbindSwipePointerHandlers() {
            if (this._swipePointerMoveHandler) {
                window.removeEventListener('pointermove', this._swipePointerMoveHandler);
                this._swipePointerMoveHandler = null;
            }

            if (this._swipePointerEndHandler) {
                window.removeEventListener('pointerup', this._swipePointerEndHandler);
                window.removeEventListener('pointercancel', this._swipePointerEndHandler);
                this._swipePointerEndHandler = null;
            }
        },

        evaluateSwipe(clientX, clientY) {
            if (this.swipeResolved || ! this.swipeActive) {
                return;
            }

            const count = getItemCount.call(this);

            if (count <= 1) {
                return;
            }

            const deltaX = clientX - this.swipeStartX;
            const deltaY = clientY - this.swipeStartY;

            if (Math.abs(deltaX) < SWIPE_THRESHOLD || Math.abs(deltaX) < Math.abs(deltaY)) {
                return;
            }

            this.swipeResolved = true;
            this.galleryDidSwipe = true;
            setTimeout(() => {
                this.galleryDidSwipe = false;
            }, 400);

            if (deltaX < 0) {
                this.next();
            } else {
                this.prev();
            }
        },

        endSwipe() {
            this.swipeActive = false;
            this.swipePointerId = null;
            this.unbindSwipePointerHandlers();
        },

        onGalleryPointerDown(event) {
            if (getItemCount.call(this) <= 1) {
                return;
            }

            if (this.isGalleryControlTarget(event.target)) {
                return;
            }

            if (event.pointerType === 'mouse' && event.button !== 0) {
                return;
            }

            event.preventDefault();

            this.swipeStartX = event.clientX;
            this.swipeStartY = event.clientY;
            this.swipeActive = true;
            this.swipePointerId = event.pointerId;
            this.swipeResolved = false;
            this.bindSwipePointerHandlers();
        },

        onGalleryPointerUp(event) {
            if (! this.swipeActive || event.pointerId !== this.swipePointerId) {
                return;
            }

            this.evaluateSwipe(event.clientX, event.clientY);
            this.endSwipe();
        },

        onGalleryPointerCancel(event) {
            if (event.pointerId !== this.swipePointerId) {
                return;
            }

            this.endSwipe();
        },
    };
}
