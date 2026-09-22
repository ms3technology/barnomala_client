/**
 * Inline format buttons: bold, italic, underline, strikethrough, code,
 * subscript, superscript, highlight, clear formatting.
 *
 * Most toggles go through Lexical's `FORMAT_TEXT_COMMAND`; highlight is
 * applied via inline `background-color` style so it round-trips through
 * JSON without a custom node.
 */
import { FORMAT_TEXT_COMMAND } from 'lexical';
import {
    IS_BOLD,
    IS_ITALIC,
    IS_UNDERLINE,
    IS_STRIKETHROUGH,
    IS_CODE,
    IS_SUBSCRIPT,
    IS_SUPERSCRIPT,
} from 'lexical';
import { $isTextNode, $getSelection, $isRangeSelection } from 'lexical';
import { createButton, createDivider } from './shared.js';
import { hasFormatFlag } from '../utils/format.js';

const FORMAT_BIT_BY_FORMAT = {
    bold: IS_BOLD,
    italic: IS_ITALIC,
    underline: IS_UNDERLINE,
    strikethrough: IS_STRIKETHROUGH,
    code: IS_CODE,
    subscript: IS_SUBSCRIPT,
    superscript: IS_SUPERSCRIPT,
};

export function mountTextFormat(editor, container, config) {
    const buttons = {};

    const registerFormatButton = (format, icon, label, title) => {
        const btn = createButton({
            icon,
            label,
            title,
            ariaLabel: title,
            attr: { 'data-toolbar-action': `format-${format}` },
        });
        btn.addEventListener('click', () => {
            editor.dispatchCommand(FORMAT_TEXT_COMMAND, format);
            editor.focus();
        });
        buttons[format] = btn;
        return btn;
    };

    const bold = registerFormatButton('bold', 'bold', null, 'Bold (Ctrl+B)');
    const italic = registerFormatButton('italic', 'italic', null, 'Italic (Ctrl+I)');
    const underline = registerFormatButton('underline', 'underline', null, 'Underline (Ctrl+U)');
    const strike = registerFormatButton('strikethrough', 'strike', null, 'Strikethrough');
    const code = registerFormatButton('code', 'code', null, 'Inline code');
    const sub = registerFormatButton('subscript', 'subscript', null, 'Subscript');
    const sup = registerFormatButton('superscript', 'superscript', null, 'Superscript');

    container.append(bold, italic, underline, strike, code, sub, sup);
    container.appendChild(createDivider());

    // Clear formatting
    const clear = createButton({
        icon: 'clear',
        title: 'Clear formatting',
        ariaLabel: 'Clear formatting',
        attr: { 'data-toolbar-action': 'clear-formatting' },
    });
    clear.addEventListener('click', () => {
        editor.update(() => {
            const selection = $getSelection();
            if (!$isRangeSelection(selection)) return;
            const nodes = selection.getNodes();
            for (const node of nodes) {
                if (!$isTextNode(node)) continue;
                const writable = node.getWritable();
                writable.setFormat(0);
                writable.setStyle('');
            }
        }, { discrete: true });
        editor.focus();
    });
    container.appendChild(clear);

    container.appendChild(createDivider());

    const refresh = () => {
        for (const [format, btn] of Object.entries(buttons)) {
            const bit = FORMAT_BIT_BY_FORMAT[format];
            if (bit === undefined) continue;
            const active = hasFormatFlag(editor, bit);
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-pressed', active ? 'true' : 'false');
        }
    };

    const unregister = editor.registerUpdateListener(() => refresh());
    refresh();

    return {
        destroy() {
            unregister();
            Object.values(buttons).forEach((b) => b.remove());
            clear.remove();
        },
    };
}

export {};
