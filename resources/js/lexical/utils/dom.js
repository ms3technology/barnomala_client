/**
 * DOM-utility helpers shared by the decorator nodes.
 *
 * Lexical exposes `$getNearestNodeFromDOMNode` (a `$`-prefixed helper that
 * must run inside an active editor scope). To trigger removal from a
 * `click` handler we walk the DOM ancestors to the closest
 * `[data-lexical-node-key]` element — a small attribute we attach to the
 * decorator root during `decorate()` / `createDOM()` — and use that key to
 * look up the live node through `$getNodeByKey`.
 */
import { $getNodeByKey } from 'lexical';

/**
 * Build the click handler that removes a decorator node given the closest
 * `[data-lexical-node-key]` ancestor of the event target.
 *
 * @param {Event} event
 * @param {string} key   The Lexical node key.
 */
export function removeByKey(event, key) {
    event.preventDefault();
    event.stopPropagation();
    const editor = window.__lexicalActiveEditor;
    if (!editor || !key) return;
    editor.update(() => {
        const node = $getNodeByKey(key);
        if (node) node.remove();
    }, { discrete: true });
}

/**
 * Walk up the DOM from `el` until we find an ancestor with the supplied
 * attribute; return its value or `null` when nothing matches.
 */
export function findAncestorAttr(el, attr) {
    let cursor = el;
    while (cursor) {
        const value = cursor.getAttribute?.(attr);
        if (value) return value;
        cursor = cursor.parentElement;
    }
    return null;
}
