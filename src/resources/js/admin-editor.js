import Quill from 'quill';
import 'quill/dist/quill.snow.css';

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
    });
}
