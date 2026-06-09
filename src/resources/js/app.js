import './bootstrap';
import './store-effects';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

function bootRichEditors() {
    if (! document.querySelector('.rich-editor')) {
        return;
    }

    import('./admin-editor.js').then(({ initRichEditors }) => initRichEditors());
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootRichEditors);
} else {
    bootRichEditors();
}
