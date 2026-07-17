import { buildDeleteConfirmMessage, confirmDialog } from './confirm-modal.js';

let confirmFormBypass = false;

function resolveConfirmPayload(form) {
    const entity = form.getAttribute('data-confirm-entity');
    const name = form.getAttribute('data-confirm-name');

    if (entity !== null && name !== null) {
        return buildDeleteConfirmMessage(entity, name);
    }

    const legacy = form.getAttribute('data-confirm');
    if (legacy) {
        const plain = legacy.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();

        return { html: legacy, plain };
    }

    return null;
}

export function initConfirmForms() {
    if (document.body.dataset.confirmFormsDelegated === '1') {
        return;
    }

    document.body.dataset.confirmFormsDelegated = '1';

    // Capture phase: chạy trước initFormSubmitLock trên form để cancel không đổi nút thành "Đang xử lý...".
    document.addEventListener('submit', async (event) => {
        if (confirmFormBypass) {
            return;
        }

        const form = event.target;
        if (! (form instanceof HTMLFormElement)) {
            return;
        }

        const payload = resolveConfirmPayload(form);
        if (! payload) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        if (! (await confirmDialog(payload))) {
            return;
        }

        confirmFormBypass = true;
        form.requestSubmit();
        confirmFormBypass = false;
    }, true);
}
