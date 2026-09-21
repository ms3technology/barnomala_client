/**
 * Text alignment controls (left / center / right / justify).
 *
 * Applies via Lexical's `FORMAT_ELEMENT_COMMAND` with one of the four
 * canonical element-format strings.
 */
import { FORMAT_ELEMENT_COMMAND } from 'lexical';
import { $getSelection, $isRangeSelection } from 'lexical';
import { createButton } from './shared.js';

const ALIGNMENTS = [
    { value: 'left', icon: 'align-left', title: 'Align left', aria: 'Align left' },
    { value: 'center', icon: 'align-center', title: 'Align center', aria: 'Align center' },
    { value: 'right', icon: 'align-right', title: 'Align right', aria: 'Align right' },
    { value: 'justify', icon: 'align-justify', title: 'Justify', aria: 'Justify' },
];

export function mountAlignment(editor, container, config) {
    const buttons = {};

    for (const alignment of ALIGNMENTS) {
        const btn = createButton({
            icon: alignment.icon,
            title: alignment.title,
            ariaLabel: alignment.aria,
            attr: { 'data-toolbar-action': `align-${alignment.value}` },
        });
        btn.addEventListener('click', () => {
            editor.dispatchCommand(FORMAT_ELEMENT_COMMAND, alignment.value);
            editor.focus();
        });
        buttons[alignment.value] = btn;
        container.appendChild(btn);
    }

    const getCurrentAlignment = () => {
        let align = 'left';
        editor.getEditorState().read(() => {
            const selection = $getSelection();
            if (!$isRangeSelection(selection)) return;
            const node = selection.anchor.getNode();
            let parent = node.getKey() === 'root' ? node : node.getParent();
            while (parent) {
                const format = parent.getFormat?.();
                if (typeof format === 'string' && ['left', 'center', 'right', 'justify'].includes(format)) {
                    align = format;
                    return;
                }
                parent = parent.getParent();
            }
        });
        return align;
    };

    const refresh = () => {
        const current = getCurrentAlignment();
        for (const [value, btn] of Object.entries(buttons)) {
            btn.classList.toggle('is-active', value === current);
            btn.setAttribute('aria-pressed', value === current ? 'true' : 'false');
        }
    };

    const unregister = editor.registerUpdateListener(() => refresh());
    refresh();

    return {
        destroy() {
            unregister();
            Object.values(buttons).forEach((b) => b.remove());
        },
    };
}
