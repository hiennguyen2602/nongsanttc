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

export function bootAdminEnhancements() {
    bootRichEditors();
    initNumberInputs();
}
