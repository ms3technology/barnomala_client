/**
 * Toolbar root.
 *
 * Mounts every toolbar module in a stable order, exposes the active editor
 * on `window.__lexicalActiveEditor` so dropdowns can snapshot the
 * selection while opening, and wires keyboard shortcuts (Ctrl/Cmd+Z/Y,
 * Ctrl/Cmd+Shift+Z, Ctrl/Cmd+B/I/U/K).
 */
import { KEY_DOWN_COMMAND, COMMAND_PRIORITY_LOW } from 'lexical';
import {
    UNDO_COMMAND,
    REDO_COMMAND,
    FORMAT_TEXT_COMMAND,
} from 'lexical';
import { TOGGLE_LINK_COMMAND } from '@lexical/link';

import { mountHistory } from './history.js';
import { mountBlockType } from './block-type.js';
import { mountFontFamily } from './font-family.js';
import { mountFontSize } from './font-size.js';
import { mountTextFormat } from './text-format.js';
import { mountColors } from './colors.js';
import { mountAlignment } from './alignment.js';
import { mountLink } from './link.js';
import { mountLists } from './list.js';
import { mountInsertMenu } from './insert-menu.js';

/**
 * @param {import('lexical').LexicalEditor} editor
 * @param {HTMLElement} container
 * @param {object} config
 */
export function mountToolbar(editor, container, config) {
    // Expose the editor so dropdown shared helpers can snapshot selection
    window.__lexicalActiveEditor = editor;

    const handles = [];

    container.classList.add('lex-toolbar');
    container.setAttribute('role', 'toolbar');
    container.setAttribute('aria-label', 'Editor toolbar');

    // History
    handles.push(mountHistory(editor, container));
    container.appendChild(divider());

    // Insert
    handles.push(mountInsertMenu(editor, container, config));
    container.appendChild(divider());

    // Block type + lists
    handles.push(mountBlockType(editor, container, config));
    container.appendChild(divider());
    handles.push(mountLists(editor, container, config));
    container.appendChild(divider());

    // Alignment (right after the checklist so it sits beside the list tools)
    handles.push(mountAlignment(editor, container, config));
    container.appendChild(divider());

    // Fonts
    handles.push(mountFontFamily(editor, container, config));
    handles.push(mountFontSize(editor, container, config));
    container.appendChild(divider());

    // Inline format
    handles.push(mountTextFormat(editor, container, config));
    container.appendChild(divider());

    // Link (sits immediately before text color / highlight color)
    handles.push(mountLink(editor, container, config));

    // Colors
    handles.push(mountColors(editor, container, config));

    // ── Keyboard shortcuts ────────────────────────────────────────────────
    const onKeyDown = (event) => {
        const meta = event.metaKey || event.ctrlKey;
        if (!meta) return false;
        const key = event.key.toLowerCase();

        if (key === 'z' && !event.shiftKey) {
            event.preventDefault();
            editor.dispatchCommand(UNDO_COMMAND, undefined);
            return true;
        }
        if ((key === 'z' && event.shiftKey) || key === 'y') {
            event.preventDefault();
            editor.dispatchCommand(REDO_COMMAND, undefined);
            return true;
        }
        if (key === 'b') {
            event.preventDefault();
            editor.dispatchCommand(FORMAT_TEXT_COMMAND, 'bold');
            return true;
        }
        if (key === 'i') {
            event.preventDefault();
            editor.dispatchCommand(FORMAT_TEXT_COMMAND, 'italic');
            return true;
        }
        if (key === 'u') {
            event.preventDefault();
            editor.dispatchCommand(FORMAT_TEXT_COMMAND, 'underline');
            return true;
        }
        if (key === 'k') {
            event.preventDefault();
            const url = window.prompt('Link URL');
            if (!url) return true;
            // Basic sanitization at this point is fine — the link popover
            // handles full validation when the user uses the toolbar UI.
            if (!/^https?:\/\//i.test(url) && !/^mailto:/i.test(url) && !/^tel:/i.test(url)) return true;
            editor.dispatchCommand(TOGGLE_LINK_COMMAND, url);
            return true;
        }
        return false;
    };

    const unregister = editor.registerCommand(KEY_DOWN_COMMAND, onKeyDown, COMMAND_PRIORITY_LOW);

    return {
        destroy() {
            unregister();
            for (const handle of handles) handle.destroy();
            container.innerHTML = '';
            container.classList.remove('lex-toolbar');
            if (window.__lexicalActiveEditor === editor) {
                window.__lexicalActiveEditor = null;
            }
        },
    };
}

function divider() {
    const sep = document.createElement('span');
    sep.className = 'lex-tb-divider';
    sep.setAttribute('aria-hidden', 'true');
    return sep;
}
