const MODAL_ID = 'gt-confirm-modal';

let modalEl = null;
let modalInstance = null;
let pendingResolve = null;
let initialized = false;

function finish(result) {
    if (! pendingResolve) {
        return;
    }

    const resolve = pendingResolve;
    pendingResolve = null;
    resolve(result);
}

function escapeHtml(text) {
    const el = document.createElement('span');
    el.textContent = text ?? '';

    return el.innerHTML;
}

/** @return {{ html: string, plain: string }} */
export function buildDeleteConfirmMessage(entityLabel, name) {
    const plain = `Bạn có chắc muốn xóa ${entityLabel} ${name}? Hành động này không thể hoàn tác.`;
    const html = `Bạn có chắc muốn xóa ${escapeHtml(entityLabel)} <span class="confirm-emphasis">${escapeHtml(name)}</span>? Hành động này không thể hoàn tác.`;

    return { html, plain };
}

/** @return {{ html: string, plain: string }} */
export function buildRemoveConfirmMessage(emphasis = 'ảnh này', suffix = '') {
    const plain = `Bạn có chắc muốn gỡ ${emphasis}?${suffix}`;
    const html = `Bạn có chắc muốn gỡ <span class="confirm-emphasis">${escapeHtml(emphasis)}</span>?${suffix ? ` ${escapeHtml(suffix)}` : ''}`;

    return { html, plain };
}

function ensureInitialized() {
    if (initialized) {
        return modalEl && modalInstance;
    }

    modalEl = document.getElementById(MODAL_ID);
    if (! modalEl || typeof bootstrap === 'undefined' || ! bootstrap.Modal) {
        return false;
    }

    modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl, {
        backdrop: true,
        keyboard: true,
        focus: true,
    });

    modalEl.querySelectorAll('[data-gt-confirm-ok]').forEach((btn) => {
        btn.addEventListener('click', () => {
            finish(true);
            modalInstance.hide();
        });
    });

    modalEl.querySelectorAll('[data-gt-confirm-cancel]').forEach((btn) => {
        btn.addEventListener('click', () => {
            finish(false);
            modalInstance.hide();
        });
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        finish(false);
    });

    modalEl.addEventListener('shown.bs.modal', () => {
        modalEl.querySelector('[data-gt-confirm-cancel]')?.focus();
    });

    initialized = true;

    return true;
}

/**
 * @param {{ html: string, plain: string }} payload
 */
export function confirmDialog(payload) {
    const html = payload.html ?? '';
    const plain = payload.plain ?? html.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();

    if (! ensureInitialized()) {
        return Promise.resolve(window.confirm(plain));
    }

    const messageEl = modalEl.querySelector('[data-gt-confirm-message]');
    if (messageEl) {
        messageEl.innerHTML = html;
    }

    return new Promise((resolve) => {
        if (pendingResolve) {
            resolve(false);

            return;
        }

        pendingResolve = resolve;
        modalInstance.show();
    });
}
