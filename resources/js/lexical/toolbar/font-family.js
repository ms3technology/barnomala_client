/**
 * Font family selector.
 *
 * Applies the value to each TextNode's inline `font-family` style via
 * `$patchStyleText` so it round-trips through serialization cleanly.
 */
import { $getSelection, $isRangeSelection } from 'lexical';
import { $patchStyleText } from '@lexical/selection';
import { createSelect } from './shared.js';
import { getActiveInlineStyle } from '../utils/format.js';

export function mountFontFamily(editor, container, config) {
    const options = [{ label: 'Default', value: '' }, ...config.fontFamilies];
    const select = createSelect({
        title: 'Font family',
        ariaLabel: 'Font family',
        options,
        attr: { 'data-toolbar-action': 'font-family' },
    });

    const apply = (value) => {
        if (!value) return;
        editor.update(() => {
            const selection = $getSelection();
            if ($isRangeSelection(selection)) {
                $patchStyleText(selection, { 'font-family': value });
            }
        }, { discrete: true });
        editor.focus();
    };

    select.addEventListener('change', () => apply(select.value));

    const refresh = () => {
        const current = getActiveInlineStyle(editor, 'font-family') || '';
        select.value = current;
        if (select.value !== current) {
            // Force a visible "no match" placeholder.
            const placeholder = document.createElement('option');
            placeholder.value = current;
            placeholder.textContent = current;
            placeholder.selected = true;
            select.insertBefore(placeholder, select.firstChild);
        }
    };

    container.appendChild(select);

    const unregister = editor.registerUpdateListener(() => refresh());
    refresh();

    return {
        destroy() {
            unregister();
            select.remove();
        },
    };
}
