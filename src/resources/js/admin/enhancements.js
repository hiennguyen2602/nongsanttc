function bootRichEditors() {
    if (! document.querySelector('.rich-editor')) {
        return;
    }

    import('../admin-editor.js').then(({ initRichEditors }) => initRichEditors());
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

function initFormSubmitLock() {
    document.querySelectorAll('form').forEach((form) => {
        if (form.dataset.submitLockInit || form.method.toLowerCase() !== 'post') {
            return;
        }

        form.dataset.submitLockInit = '1';

        form.addEventListener('submit', (event) => {
            if (form.dataset.submitting === '1') {
                event.preventDefault();
                return;
            }

            const submitters = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            if (!submitters.length) {
                return;
            }

            form.dataset.submitting = '1';

            submitters.forEach((button) => {
                if (!button.dataset.submitLabel) {
                    button.dataset.submitLabel = button.tagName === 'INPUT'
                        ? button.value
                        : button.textContent.trim();
                }

                button.disabled = true;
                button.classList.add('is-submitting');
                button.setAttribute('aria-busy', 'true');

                if (button.tagName === 'BUTTON') {
                    button.textContent = 'Đang xử lý...';
                } else {
                    button.value = 'Đang xử lý...';
                }
            });
        });
    });
}

export function bootAdminEnhancements() {
    bootRichEditors();
    initNumberInputs();
    initFormSubmitLock();
}
