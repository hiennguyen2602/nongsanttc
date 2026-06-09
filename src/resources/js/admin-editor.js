import Quill from 'quill';
import 'quill/dist/quill.snow.css';

export function initRichEditors() {
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
                        [{ font: [] }, { size: ['small', false, 'large', 'huge'] }],
                        [{ header: [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ color: [] }, { background: [] }],
                        [{ script: 'sub' }, { script: 'super' }],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ indent: '-1' }, { indent: '+1' }],
                        [{ align: [] }],
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
