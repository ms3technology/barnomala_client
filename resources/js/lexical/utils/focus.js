/**
 * Selection-preservation helpers.
 *
 * Toolbar interactions normally move focus to the toolbar (so the contenteditable
 * element loses its selection). Lexical's `editor.focus()` restores the DOM
 * focus but not the previous `RangeSelection` — we save & restore the editor
 * selection explicitly so applying a format or opening a dropdown does not
 * discard the user's current selection.
 */
import { $getSelection, $isRangeSelection, $setSelection } from 'lexical';

/**
 * Cache the current Lexical selection. Returns a `restore()` function that
 * re-applies it to the editor without throwing when invoked outside of an
 * `update()` callback (Lexical handles that gracefully).
 */
export function snapshotSelection(editor) {
    let snapshot = null;

    try {
        editor.getEditorState().read(() => {
            const selection = $getSelection();
            if ($isRangeSelection(selection)) {
                snapshot = selection.clone();
            }
        });
    } catch (_) {
        snapshot = null;
    }

    return () => {
        if (!snapshot) {
            editor.focus();
            return;
        }
        editor.update(() => {
            try {
                $setSelection(snapshot);
            } catch (_) {
                // Selection may have become invalid if the document mutated.
            }
        }, { discrete: true });
        editor.focus();
    };
}

/**
 * Run `fn(selection)` inside an `editor.update()` block with the current
 * range selection as the argument. After the callback returns, focus is
 * returned to the editor and the selection is restored.
 */
export function withSelection(editor, fn) {
    let result;
    editor.update(() => {
        const selection = $getSelection();
        if ($isRangeSelection(selection)) {
            result = fn(selection);
        }
    }, { discrete: true });
    editor.focus();
    return result;
}
