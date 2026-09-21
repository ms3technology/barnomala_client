/**
 * Font size controls.
 *
 * Exposes a `<select>` for picking an explicit size plus two buttons for
 * stepping one size up or down. Sizes are tracked in px via inline style.
 */
import { $getSelection, $isRangeSelection } from 'lexical';
import { $patchStyleText } from '@lexical/selection';
import { createButton, createSelect } from './shared.js';
import { getActiveInlineStyle } from '../utils/format.js';

function parseSize(px) {
    if (!px) return null;
    const match = /^(\d+(?:\.\d+)?)px$/.exec(px.trim());
    return match ? parseFloat(match[1]) : null;
}

function formatSize(px) {
    return `${Math.round(px)}px`;
}

export function mountFontSize(editor, container, config) {
    const options = [{ label: 'Default', value: '' }, ...config.fontSizes];

    const select = createSelect({
        title: 'Font size',
        ariaLabel: 'Font size',
        options,
        attr: { 'data-toolbar-action': 'font-size' },
    });

    const apply = (value) => {
        editor.update(() => {
            const selection = $getSelection();
            if ($isRangeSelection(selection)) {
                $patchStyleText(selection, { 'font-size': value });
            }
        }, { discrete: true });
        editor.focus();
    };

    select.addEventListener('change', () => apply(select.value));

    const incBtn = createButton({
        icon: 'plus',
        title: 'Increase font size',
        ariaLabel: 'Increase font size',
        attr: { 'data-toolbar-action': 'font-size-inc' },
    });
    const decBtn = createButton({
        icon: 'minus',
        title: 'Decrease font size',
        ariaLabel: 'Decrease font size',
        attr: { 'data-toolbar-action': 'font-size-dec' },
    });

    const sizes = config.fontSizes.map((opt) => parseSize(opt.value)).filter((n) => n);

    const step = (delta) => {
        const current = parseSize(getActiveInlineStyle(editor, 'font-size')) || 16;
        const sorted = [...sizes].sort((a, b) => a - b);
        let next = current + delta;
        next = Math.max(sorted[0], Math.min(sorted[sorted.length - 1], next));
        // Snap to nearest size option
        const snapped = sorted.reduce((acc, value) => (Math.abs(value - next) < Math.abs(acc - next) ? value : acc), sorted[0]);
        apply(formatSize(snapped));
    };

    incBtn.addEventListener('click', () => step(2));
    decBtn.addEventListener('click', () => step(-2));

    const refresh = () => {
        const current = getActiveInlineStyle(editor, 'font-size') || '';
        if (Array.from(select.options).some((o) => o.value === current)) {
            select.value = current;
        } else {
            select.value = '';
        }
    };

    container.appendChild(decBtn);
    container.appendChild(select);
    container.appendChild(incBtn);

    const unregister = editor.registerUpdateListener(() => refresh());
    refresh();

    return {
        destroy() {
            unregister();
            decBtn.remove();
            incBtn.remove();
            select.remove();
        },
    };
}
