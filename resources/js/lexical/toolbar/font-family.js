/**
 * Font family selector.
 *
 * Applies the value to each TextNode's inline `font-family` style via
 * `$patchStyleText` so it round-trips through serialization cleanly.
 */
import { $getSelection, $isRangeSelection, $setSelection, $getRoot } from 'lexical';
import { $patchStyleText } from '@lexical/selection';
import { createSelect } from './shared.js';
import { getActiveInlineStyle } from '../utils/format.js';

function normalizeFontFamily(str) {
    return str ? str.replace(/['"]/g, '').toLowerCase().trim() : '';
}

export function mountFontFamily(editor, container, config) {
    const options = [{ label: 'Default Font', value: '' }, ...config.fontFamilies];
    let lastSelection = null;

    const select = createSelect({
        title: 'Font family',
        ariaLabel: 'Font family',
        options,
        attr: { 'data-toolbar-action': 'font-family' },
    });

    const apply = (value) => {
        editor.update(() => {
            if (lastSelection) {
                try {
                    $setSelection(lastSelection);
                } catch (_) {}
            }
            let selection = $getSelection();
            if (!$isRangeSelection(selection)) {
                const root = $getRoot();
                const firstChild = root.getFirstChild();
                if (firstChild) {
                    firstChild.select();
                    selection = $getSelection();
                }
            }
            if ($isRangeSelection(selection)) {
                $patchStyleText(selection, { 'font-family': value || null });
            }
        }, { discrete: true });
        editor.focus();
    };

    const snapshot = () => {
        editor.getEditorState().read(() => {
            const selection = $getSelection();
            if ($isRangeSelection(selection)) {
                lastSelection = selection.clone();
            }
        });
    };

    const unregister = editor.registerUpdateListener(({ editorState }) => {
        editorState.read(() => {
            const selection = $getSelection();
            if ($isRangeSelection(selection)) {
                lastSelection = selection.clone();
            }
        });
        refresh();
    });

    select.addEventListener('focus', snapshot);
    select.addEventListener('mousedown', snapshot);

    select.addEventListener('change', () => {
        apply(select.value);
    });

    const refresh = () => {
        const current = getActiveInlineStyle(editor, 'font-family') || '';
        const normalized = normalizeFontFamily(current);
        const match = Array.from(select.options).find(
            (opt) => normalizeFontFamily(opt.value) === normalized
        );
        select.value = match ? match.value : '';
    };

    container.appendChild(select);
    refresh();

    return {
        destroy() {
            unregister();
            select.remove();
        },
    };
}
