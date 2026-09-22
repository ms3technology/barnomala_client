/**
 * Text color + background color pickers.
 *
 * Each control is a button that toggles a popover grid of color swatches.
 * Picking a swatch applies the color via `$patchStyleText`, which keeps
 * the value as inline style on TextNodes (round-trip safe through JSON).
 */
import { $getSelection, $isRangeSelection } from 'lexical';
import { $patchStyleText } from '@lexical/selection';
import { createDropdown } from './shared.js';
import { getActiveInlineStyle } from '../utils/format.js';

function buildSwatchGrid(editor, colors, property, dropdown) {
    const grid = document.createElement('div');
    grid.className = 'lex-tb-color-grid';
    for (const color of colors) {
        const swatch = document.createElement('button');
        swatch.type = 'button';
        swatch.className = 'lex-tb-color-swatch';
        swatch.setAttribute('data-color', color);
        swatch.title = color === 'transparent' ? 'No color' : color;
        swatch.setAttribute('aria-label', color === 'transparent' ? 'No color' : `Apply ${color}`);
        swatch.style.background = color === 'transparent' ? 'transparent' : color;
        if (color === 'transparent') swatch.classList.add('lex-tb-color-swatch--none');
        swatch.addEventListener('mousedown', (event) => event.preventDefault());
        swatch.addEventListener('click', (event) => {
            event.preventDefault();
            editor.update(() => {
                const selection = $getSelection();
                if ($isRangeSelection(selection)) {
                    $patchStyleText(selection, { [property]: color === 'transparent' ? '' : color });
                }
            }, { discrete: true });
            dropdown.close();
            editor.focus();
        });
        grid.appendChild(swatch);
    }
    return grid;
}

function makeColorPicker(editor, { label, icon, title, colors, property }) {
    const content = document.createElement('div');
    content.className = 'lex-tb-menu lex-tb-menu--colors';
    const dropdown = createDropdown({ label, icon, title, content });
    content.appendChild(buildSwatchGrid(editor, colors, property, dropdown));
    return dropdown;
}

export function mountColors(editor, container, config) {
    const textDropdown = makeColorPicker(editor, {
        label: '',
        icon: 'text-color',
        title: 'Text color',
        colors: config.textColors,
        property: 'color',
    });
    const highlightDropdown = makeColorPicker(editor, {
        label: '',
        icon: 'highlight',
        title: 'Highlight color',
        colors: config.highlightColors,
        property: 'background-color',
    });

    container.appendChild(textDropdown.root);
    container.appendChild(highlightDropdown.root);

    const refresh = () => {
        const textColor = getActiveInlineStyle(editor, 'color');
        const textBtn = textDropdown.root.querySelector('.lex-tb-dropdown__toggle');
        if (textBtn) {
            textBtn.style.setProperty('--lex-tb-underline-color', textColor || '');
            textBtn.classList.toggle('has-color', !!textColor);
        }
        const bgColor = getActiveInlineStyle(editor, 'background-color');
        const bgBtn = highlightDropdown.root.querySelector('.lex-tb-dropdown__toggle');
        if (bgBtn) {
            bgBtn.style.setProperty('--lex-tb-highlight', bgColor || '');
            bgBtn.classList.toggle('has-color', !!bgColor);
        }
    };

    const unregister = editor.registerUpdateListener(() => refresh());
    refresh();

    return {
        destroy() {
            unregister();
            textDropdown.destroy();
            highlightDropdown.destroy();
            textDropdown.root.remove();
            highlightDropdown.root.remove();
        },
    };
}
