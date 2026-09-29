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

function lockConfirmTrigger(button) {
    if (! button.dataset.submitLabel) {
        button.dataset.submitLabel = button.textContent.trim();
    }

    button.disabled = true;
    button.classList.add('is-submitting');
    button.setAttribute('aria-busy', 'true');
    button.textContent = 'Đang xử lý...';
}

async function confirmAndSubmit(form, triggerButton = null) {
    const payload = resolveConfirmPayload(form);
    if (! payload) {
        return;
    }

    if (! (await confirmDialog(payload))) {
        return;
    }

    if (triggerButton) {
        lockConfirmTrigger(triggerButton);
    }

    confirmFormBypass = true;
    form.requestSubmit();
    confirmFormBypass = false;
}

export function initConfirmForms() {
    if (document.body.dataset.confirmFormsDelegated === '1') {
        return;
    }

    document.body.dataset.confirmFormsDelegated = '1';

    document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-confirm-trigger]');
        if (! button) {
            return;
        }

        const form = button.closest('form');
        if (! (form instanceof HTMLFormElement)) {
            return;
        }

        event.preventDefault();

        await confirmAndSubmit(form, button);
    }, true);

    // Legacy: form vẫn dùng type="submit" hoặc submit từ nơi khác.
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
