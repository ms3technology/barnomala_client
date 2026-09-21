/**
 * Inline format buttons: bold, italic, underline, strikethrough, code,
 * subscript, superscript, highlight, clear formatting.
 *
 * Most toggles go through Lexical's `FORMAT_TEXT_COMMAND`; highlight is
 * applied via inline `background-color` style so it round-trips through
 * JSON without a custom node.
 */
import {
    FORMAT_TEXT_COMMAND,
    $isTextNode,
    $getSelection,
    $isRangeSelection,
    SELECTION_CHANGE_COMMAND,
    COMMAND_PRIORITY_LOW,
} from 'lexical';
import { $patchStyleText } from '@lexical/selection';
import { $setBlocksType } from 'lexical';
import { $createParagraphNode } from 'lexical';
import { createButton, createDivider } from './shared.js';
import {
    IS_BOLD,
    IS_ITALIC,
    IS_UNDERLINE,
    IS_STRIKETHROUGH,
    IS_CODE,
    IS_SUBSCRIPT,
    IS_SUPERSCRIPT,
} from 'lexical';
import { hasFormatFlag, getActiveInlineStyle } from '../utils/format.js';

const FORMAT_BIT_BY_FORMAT = {
    bold: IS_BOLD,
    italic: IS_ITALIC,
    underline: IS_UNDERLINE,
    strikethrough: IS_STRIKETHROUGH,
    code: IS_CODE,
    subscript: IS_SUBSCRIPT,
    superscript: IS_SUPERSCRIPT,
};

/**
 * Read the active highlight color for the current selection.
 */
function getActiveHighlight(editor) {
    return getActiveInlineStyle(editor, 'background-color') || '';
}

/**
 * Read the active text color for the current selection.
 */
function getActiveTextColor(editor) {
    return getActiveInlineStyle(editor, 'color') || '';
}

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

    // Highlight — toggle button + small palette accessible via "More".
    const highlight = createButton({
        icon: 'highlight',
        title: 'Highlight color',
        ariaLabel: 'Highlight color',
        attr: { 'data-toolbar-action': 'format-highlight' },
    });
    const applyHighlight = (color) => {
        editor.update(() => {
            const selection = $getSelection();
            if ($isRangeSelection(selection)) {
                $patchStyleText(selection, { 'background-color': color });
            }
        }, { discrete: true });
        editor.focus();
    };
    highlight.addEventListener('click', () => {
        const current = getActiveHighlight(editor);
        const idx = config.highlightColors.indexOf(current);
        const next = config.highlightColors[(idx + 1) % config.highlightColors.length] || config.highlightColors[0];
        applyHighlight(next);
    });
    buttons.highlight = highlight;
    container.appendChild(highlight);

    // Clear formatting
    const clear = createButton({
        icon: 'clear',
        title: 'Clear formatting',
        ariaLabel: 'Clear formatting',
        attr: { 'data-toolbar-action': 'clear-formatting' },
    });
    const clearFormatting = () => {
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
    };
    clear.addEventListener('click', clearFormatting);
    container.appendChild(clear);

    container.appendChild(createDivider());

    const refresh = () => {
        for (const [format, btn] of Object.entries(buttons)) {
            if (format === 'highlight') {
                const c = getActiveHighlight(editor);
                btn.classList.toggle('is-active', !!c);
                if (c) btn.style.setProperty('--lex-tb-highlight', c);
                else btn.style.removeProperty('--lex-tb-highlight');
                continue;
            }
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

// Re-export the highlight/text color getters so colors.js can re-use them
// without importing the format module directly.
export { getActiveHighlight, getActiveTextColor, applyHighlight };
