import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import { buildRemoveConfirmMessage, confirmDialog } from './admin/confirm-modal.js';

let formatsRegistered = false;

const FONT_WHITELIST = ['Arial', 'Times New Roman', 'Georgia', 'Tahoma', 'Verdana', 'Courier New'];
const SIZE_WHITELIST = ['10px', '12px', '14px', '16px', '18px', '20px', '24px', '28px', '32px', '40px', '48px'];

function registerFormats() {
    if (formatsRegistered) {
        return;
    }
    formatsRegistered = true;

    const Font = Quill.import('attributors/style/font');
    Font.whitelist = FONT_WHITELIST;
    Quill.register(Font, true);

    const Size = Quill.import('attributors/style/size');
    Size.whitelist = SIZE_WHITELIST;
    Quill.register(Size, true);

    const Align = Quill.import('attributors/style/align');
    Quill.register(Align, true);
}

export function initRichEditors() {
    registerFormats();

    document.querySelectorAll('.rich-editor').forEach((wrapper) => {
        if (wrapper.dataset.initialized) {
            return;
        }

        wrapper.dataset.initialized = '1';

        const textarea = wrapper.querySelector('.rich-editor-input');
        const area = wrapper.querySelector('.rich-editor-area');
        const uploadUrl = wrapper.dataset.uploadUrl;
        const deleteUrl = wrapper.dataset.deleteUrl;
        const urlToPath = new Map();

        const quill = new Quill(area, {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        [{ font: FONT_WHITELIST }, { size: SIZE_WHITELIST }],
                        [{ header: [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ color: [] }, { background: [] }],
                        [{ script: 'sub' }, { script: 'super' }],
                        [{ list: 'ordered' }, { list: 'bullet' }, { list: 'check' }],
                        [{ indent: '-1' }, { indent: '+1' }],
                        [{ align: [] }, { direction: 'rtl' }],
                        ['blockquote', 'code-block'],
                        ['link', 'image', 'video'],
                        ['clean'],
                    ],
                    handlers: {
                        image: () => {
                            const input = document.createElement('input');
                            input.type = 'file';
                            input.accept = 'image/*';
                            input.onchange = async () => {
                                const file = input.files?.[0];
                                if (! file) {
                                    return;
                                }

                                const formData = new FormData();
                                formData.append('file', file);

                                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                                const response = await fetch(uploadUrl, {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': token },
                                    body: formData,
                                });

                                if (! response.ok) {
                                    return;
                                }

                                const data = await response.json();
                                if (data.path) {
                                    urlToPath.set(data.url, data.path);
                                }
                                const range = quill.getSelection(true);
                                quill.insertEmbed(range.index, 'image', data.url);
                                quill.setSelection(range.index + 1);
                            };
                            input.click();
                        },
                    },
                },
            },
        });

        if (textarea.value) {
            quill.root.innerHTML = textarea.value;
        }

        quill.on('text-change', () => {
            textarea.value = quill.root.innerHTML;
        });

        wrapper.closest('form')?.addEventListener('submit', () => {
            textarea.value = quill.root.innerHTML;
        });

        if (deleteUrl) {
            attachImageDeletion(quill, wrapper, deleteUrl, urlToPath);
        }
    });
}

function deleteServerImage(src, deleteUrl, urlToPath) {
    let path = urlToPath.get(src);

    if (! path) {
        try {
            path = new URL(src, window.location.origin).pathname.replace(/^\/+/, '');
        } catch (e) {
            return;
        }
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch(deleteUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({ path }),
    }).catch(() => {});
}

function attachImageDeletion(quill, wrapper, deleteUrl, urlToPath) {
    const editorRoot = quill.root;

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'rich-editor-img-delete';
    btn.innerHTML = '&times;';
    btn.title = 'Xóa ảnh';
    wrapper.appendChild(btn);

    let currentImg = null;
    let hideTimer = null;

    const position = () => {
        if (! currentImg) {
            return;
        }
        const imgRect = currentImg.getBoundingClientRect();
        const wrapRect = wrapper.getBoundingClientRect();
        btn.style.top = (imgRect.top - wrapRect.top + 6) + 'px';
        btn.style.left = (imgRect.right - wrapRect.left - 30) + 'px';
    };

    const show = (img) => {
        clearTimeout(hideTimer);
        currentImg = img;
        position();
        btn.classList.add('is-visible');
    };

    const scheduleHide = () => {
        hideTimer = setTimeout(() => {
            if (! btn.matches(':hover')) {
                btn.classList.remove('is-visible');
                currentImg = null;
            }
        }, 150);
    };

    editorRoot.addEventListener('mouseover', (event) => {
        if (event.target.tagName === 'IMG') {
            show(event.target);
        }
    });

    editorRoot.addEventListener('mouseout', (event) => {
        if (event.target.tagName === 'IMG') {
            scheduleHide();
        }
    });

    editorRoot.addEventListener('scroll', position);
    btn.addEventListener('mouseleave', scheduleHide);

    btn.addEventListener('click', async () => {
        if (! currentImg) {
            return;
        }

        const confirmed = await confirmDialog(
            buildRemoveConfirmMessage('ảnh này khỏi nội dung', 'Thao tác này không thể hoàn tác.'),
        );

        if (! confirmed) {
            return;
        }

        const src = currentImg.getAttribute('src');
        const blot = Quill.find(currentImg);

        if (blot) {
            quill.deleteText(quill.getIndex(blot), 1);
        }

        deleteServerImage(src, deleteUrl, urlToPath);
        btn.classList.remove('is-visible');
        currentImg = null;
    });
}
