/**
 * History controls: undo + redo.
 */
import {
    UNDO_COMMAND,
    REDO_COMMAND,
    CAN_UNDO_COMMAND,
    CAN_REDO_COMMAND,
} from 'lexical';
import { createButton } from './shared.js';

export function mountHistory(editor, container) {
    const undoBtn = createButton({
        label: 'Undo',
        icon: 'undo',
        title: 'Undo (Ctrl+Z)',
        ariaLabel: 'Undo',
        attr: { 'data-toolbar-action': 'undo' },
    });
    const redoBtn = createButton({
        label: 'Redo',
        icon: 'redo',
        title: 'Redo (Ctrl+Shift+Z)',
        ariaLabel: 'Redo',
        attr: { 'data-toolbar-action': 'redo' },
    });

    undoBtn.addEventListener('click', () => editor.dispatchCommand(UNDO_COMMAND, undefined));
    redoBtn.addEventListener('click', () => editor.dispatchCommand(REDO_COMMAND, undefined));

    container.appendChild(undoBtn);
    container.appendChild(redoBtn);

    // Disable state
    let canUndo = false;
    let canRedo = false;

    const refresh = () => {
        undoBtn.disabled = !canUndo;
        redoBtn.disabled = !canRedo;
        undoBtn.classList.toggle('is-disabled', !canUndo);
        redoBtn.classList.toggle('is-disabled', !canRedo);
    };

    editor.registerCommand(CAN_UNDO_COMMAND, (payload) => {
        canUndo = !!payload;
        refresh();
        return false;
    }, 1);

    editor.registerCommand(CAN_REDO_COMMAND, (payload) => {
        canRedo = !!payload;
        refresh();
        return false;
    }, 1);

    refresh();

    return {
        destroy() {
            undoBtn.remove();
            redoBtn.remove();
        },
    };
}
