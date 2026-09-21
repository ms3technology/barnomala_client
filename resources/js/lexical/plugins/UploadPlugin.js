/**
 * UploadPlugin.
 *
 * When the user pastes or drops an image file into the contenteditable, we
 * upload it to the Laravel endpoint and dispatch `INSERT_IMAGE_COMMAND`
 * with the returned URL. The server is authoritative for validation; this
 * plugin just gates by MIME so we never send huge or non-image files.
 */
import { INSERT_IMAGE_COMMAND } from '../commands/registry.js';

const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
const MAX_BYTES = 5 * 1024 * 1024; // 5 MB

/**
 * Upload a single file to the configured endpoint. Returns `{src, altText}`
 * on success or `null` on failure (with a console warning).
 */
async function uploadFile(editor, file, config) {
    if (!ALLOWED_MIME_TYPES.includes(file.type)) {
        // eslint-disable-next-line no-console
        console.warn('[lexical] rejected file type', file.type);
        return null;
    }
    if (file.size > MAX_BYTES) {
        // eslint-disable-next-line no-console
        console.warn('[lexical] file too large', file.size);
        return null;
    }
    if (!config.uploadUrl) {
        // eslint-disable-next-line no-console
        console.warn('[lexical] no uploadUrl configured');
        return null;
    }

    const formData = new FormData();
    formData.append('image', file, file.name);

    try {
        const response = await fetch(config.uploadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': config.csrf || '',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
            credentials: 'same-origin',
        });
        if (!response.ok) {
            // eslint-disable-next-line no-console
            console.warn('[lexical] upload failed', response.status);
            return null;
        }
        const data = await response.json();
        if (!data || !data.url) {
            return null;
        }
        return { src: data.url, altText: data.alt || '' };
    } catch (error) {
        // eslint-disable-next-line no-console
        console.warn('[lexical] upload error', error);
        return null;
    }
}

/**
 * Register drag/drop + paste handlers on the editor root. Only files
 * accepted by `ALLOWED_MIME_TYPES` are forwarded to the upload endpoint.
 */
export function registerUploadHandler(editor, config) {
    const rootEl = editor.getRootElement();

    const handleFiles = (files) => {
        const accepted = Array.from(files || []).filter((file) => ALLOWED_MIME_TYPES.includes(file.type));
        if (!accepted.length) return;
        for (const file of accepted) {
            uploadFile(editor, file, config).then((result) => {
                if (result) {
                    editor.dispatchCommand(INSERT_IMAGE_COMMAND, result);
                }
            });
        }
    };

    if (!rootEl) return () => {};

    const onPaste = (event) => {
        const items = event.clipboardData?.items;
        if (!items) return;
        const files = [];
        for (const item of items) {
            if (item.kind === 'file') {
                const file = item.getAsFile();
                if (file) files.push(file);
            }
        }
        if (files.length) {
            event.preventDefault();
            handleFiles(files);
        }
    };

    const onDrop = (event) => {
        const files = event.dataTransfer?.files;
        if (files && files.length) {
            event.preventDefault();
            handleFiles(files);
        }
    };

    const onDragOver = (event) => {
        // Required so drop fires. We never actually let the editor render
        // the dragged image — we upload and re-insert via the command.
        if (event.dataTransfer && Array.from(event.dataTransfer.types || []).includes('Files')) {
            event.preventDefault();
        }
    };

    rootEl.addEventListener('paste', onPaste);
    rootEl.addEventListener('drop', onDrop);
    rootEl.addEventListener('dragover', onDragOver);

    return () => {
        rootEl.removeEventListener('paste', onPaste);
        rootEl.removeEventListener('drop', onDrop);
        rootEl.removeEventListener('dragover', onDragOver);
    };
}
