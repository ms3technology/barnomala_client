/**
 * Insert dropdown — list of items that dispatch a command / open a prompt.
 *
 * Only items whose handler is implemented in `editor.js` are surfaced, so
 * we never expose a fake / non-functional menu entry.
 */
import { createDropdown, createButton } from './shared.js';
import { snapshotSelection } from '../utils/focus.js';
import {
    INSERT_HORIZONTAL_RULE_COMMAND,
    INSERT_PAGE_BREAK_COMMAND,
    INSERT_IMAGE_COMMAND,
    INSERT_YOUTUBE_COMMAND,
    INSERT_COLLAPSIBLE_COMMAND,
    INSERT_CODE_BLOCK_COMMAND,
    INSERT_DATE_COMMAND,
} from '../commands/registry.js';
import { INSERT_TABLE_COMMAND } from '@lexical/table';
import { extractYouTubeId, sanitizeUrl } from '../utils/sanitize.js';

function makeItemButton({ label, icon, onClick }) {
    const btn = createButton({
        label,
        icon,
        title: label,
        ariaLabel: label,
        attr: { role: 'menuitem' },
    });
    btn.style.justifyContent = 'flex-start';
    btn.style.width = '100%';
    btn.addEventListener('click', (event) => {
        event.preventDefault();
        onClick(btn);
    });
    return btn;
}

export function mountInsertMenu(editor, container, config) {
    const list = document.createElement('div');
    list.className = 'lex-tb-menu';
    list.setAttribute('role', 'menu');

    const restore = () => snapshotSelection(editor)();

    const items = config.insertMenu;

    const handlers = {
        'horizontal-rule': (close) => {
            editor.dispatchCommand(INSERT_HORIZONTAL_RULE_COMMAND, undefined);
            close();
        },
        'page-break': (close) => {
            editor.dispatchCommand(INSERT_PAGE_BREAK_COMMAND, undefined);
            close();
        },
        'image': (close) => {
            // Pick file → upload → insert.
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/png,image/jpeg,image/webp,image/gif';
            input.addEventListener('change', async () => {
                const file = input.files?.[0];
                if (!file) return;
                const result = await uploadImage(editor, file, config);
                if (result) {
                    editor.dispatchCommand(INSERT_IMAGE_COMMAND, result);
                }
                close();
            });
            input.click();
        },
        'table': (close) => {
            const rows = window.prompt('Rows?', '3');
            const cols = window.prompt('Columns?', '3');
            const rowsNum = parseInt(rows || '0', 10);
            const colsNum = parseInt(cols || '0', 10);
            if (!rowsNum || !colsNum) {
                close();
                return;
            }
            editor.dispatchCommand(INSERT_TABLE_COMMAND, { rows: rowsNum, columns: colsNum });
            close();
        },
        'code-block': (close) => {
            const language = window.prompt('Language? (javascript, css, html, php, etc.)', 'javascript');
            editor.dispatchCommand(INSERT_CODE_BLOCK_COMMAND, { language: language || 'javascript' });
            close();
        },
        'link': (close) => {
            const url = window.prompt('Link URL');
            const safe = sanitizeUrl(url || '');
            if (!safe) {
                close();
                return;
            }
            // Lazy import so the toolbar doesn't pull in the link plugin
            // for users who never use this item.
            import('@lexical/link').then(({ TOGGLE_LINK_COMMAND }) => {
                editor.dispatchCommand(TOGGLE_LINK_COMMAND, safe);
            });
            close();
        },
        'youtube': (close) => {
            const url = window.prompt('YouTube URL');
            const id = extractYouTubeId(url || '');
            if (!id) {
                close();
                return;
            }
            editor.dispatchCommand(INSERT_YOUTUBE_COMMAND, id);
            close();
        },
        'date': (close) => {
            editor.dispatchCommand(INSERT_DATE_COMMAND, undefined);
            close();
        },
        'collapsible': (close) => {
            editor.dispatchCommand(INSERT_COLLAPSIBLE_COMMAND, undefined);
            close();
        },
    };

    for (const item of items) {
        const handler = handlers[item.value];
        if (!handler) continue;
        const btn = makeItemButton({
            label: item.label,
            icon: item.icon,
            onClick: (node) => {
                // Restore selection inside the editor before dispatching
                // so the new node lands where the cursor was.
                restore();
                handler(() => dropdown.close());
            },
        });
        list.appendChild(btn);
    }

    const dropdown = createDropdown({
        label: 'Insert',
        icon: 'plus',
        title: 'Insert',
        content: list,
    });
    container.appendChild(dropdown.root);

    return {
        destroy() {
            dropdown.root.remove();
        },
    };
}

async function uploadImage(editor, file, config) {
    if (!config.uploadUrl) return null;
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
        if (!response.ok) return null;
        const data = await response.json();
        if (!data || !data.url) return null;
        return { src: data.url, altText: data.alt || '' };
    } catch (error) {
        return null;
    }
}
