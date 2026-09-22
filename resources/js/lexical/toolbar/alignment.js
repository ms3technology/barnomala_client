/**
 * Text alignment controls (left / center / right / justify).
 *
 * Applies via Lexical's `FORMAT_ELEMENT_COMMAND` with one of the four
 * canonical element-format strings.
 */
import { FORMAT_ELEMENT_COMMAND } from 'lexical';
import { createButton } from './shared.js';
import { getActiveAlignment } from '../utils/format.js';

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

    const refresh = () => {
        const current = getActiveAlignment(editor);
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
