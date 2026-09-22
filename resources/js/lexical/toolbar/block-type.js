/**
 * Block type dropdown — Paragraph / H1-H6 / Quote.
 *
 * Replaces the current block via `FORMAT_ELEMENT_COMMAND`. The dropdown
 * label stays in sync with `getActiveBlockType` on every editor update.
 */
import { FORMAT_ELEMENT_COMMAND } from 'lexical';
import { $createHeadingNode, $createQuoteNode } from '@lexical/rich-text';
import { $setBlocksType } from '@lexical/selection';
import { $createParagraphNode, $getSelection, $isRangeSelection } from 'lexical';
import { createDropdown, createButton } from './shared.js';
import { getActiveBlockType } from '../utils/format.js';

export function mountBlockType(editor, container, config) {
    const list = document.createElement('div');
    list.className = 'lex-tb-menu';
    list.setAttribute('role', 'menu');

    const labelMap = {
        paragraph: 'Paragraph',
        h1: 'Heading 1',
        h2: 'Heading 2',
        h3: 'Heading 3',
        h4: 'Heading 4',
        h5: 'Heading 5',
        h6: 'Heading 6',
        quote: 'Quote',
        ul: 'Bulleted list',
        ol: 'Numbered list',
        check: 'Checklist',
        code: 'Code block',
        table: 'Table',
    };

    let labelButton = null;
    const updateLabel = () => {
        if (!labelButton) return;
        const current = getActiveBlockType(editor);
        labelButton.firstChild.textContent = labelMap[current] || 'Paragraph';
    };

    for (const option of config.blockTypes) {
        const btn = createButton({
            label: option.label,
            title: option.label,
            ariaLabel: option.label,
            attr: { 'data-block-type': option.value, role: 'menuitem' },
        });
        btn.style.justifyContent = 'flex-start';
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            setBlockType(option.value);
            dropdown.close();
        });
        list.appendChild(btn);
    }

    const dropdown = createDropdown({
        label: 'Paragraph',
        icon: 'paragraph',
        title: 'Block type',
        content: list,
    });
    labelButton = dropdown.root.querySelector('.lex-tb-dropdown__toggle .lex-tb-label');

    const setBlockType = (type) => {
        if (type === 'paragraph') {
            editor.update(() => {
                const selection = $getSelection();
                if ($isRangeSelection(selection)) {
                    $setBlocksType(selection, () => $createParagraphNode());
                }
            }, { discrete: true });
            return;
        }
        if (/^h[1-6]$/.test(type)) {
            editor.update(() => {
                const selection = $getSelection();
                if ($isRangeSelection(selection)) {
                    $setBlocksType(selection, () => $createHeadingNode(type));
                }
            }, { discrete: true });
            return;
        }
        if (type === 'quote') {
            editor.update(() => {
                const selection = $getSelection();
                if ($isRangeSelection(selection)) {
                    $setBlocksType(selection, () => $createQuoteNode());
                }
            }, { discrete: true });
            return;
        }
        editor.dispatchCommand(FORMAT_ELEMENT_COMMAND, type);
    };

    container.appendChild(dropdown.root);

    const unregister = editor.registerUpdateListener(() => updateLabel());
    updateLabel();

    return {
        destroy() {
            unregister();
            dropdown.destroy();
            dropdown.root.remove();
        },
    };
}
