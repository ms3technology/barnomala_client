/**
 * Format-state helpers.
 *
 * These functions read the current editor selection and return booleans /
 * strings describing the active block, format flags, font size, etc. The
 * toolbar wires them up to `editor.registerUpdateListener` so the UI
 * stays in sync with the cursor without re-rendering.
 */
import { $getSelection, $isRangeSelection, $isTextNode, $getRoot } from 'lexical';
import {
    $isHeadingNode,
    $isQuoteNode,
} from '@lexical/rich-text';
import { $isListNode, $isListItemNode } from '@lexical/list';
import { $isLinkNode } from '@lexical/link';
import { $isTableCellNode } from '@lexical/table';
import { $isCodeNode } from '@lexical/code';

/**
 * Walk up from `node` to its nearest block-level ancestor and return a
 * canonical block-type string ('paragraph' | 'h1' | ... | 'quote' | 'code'
 * | 'list' | 'table').
 */
export function getActiveBlockType(editor) {
    let blockType = 'paragraph';
    editor.getEditorState().read(() => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) return;
        const anchor = selection.anchor.getNode();
        let element = anchor.getKey() === 'root' ? anchor : anchor.getParent();
        while (element !== null) {
            const type = element.getType();
            if (type === 'paragraph') {
                blockType = 'paragraph';
                return;
            }
            if ($isHeadingNode(element)) {
                const tag = element.getTag();
                blockType = tag;
                return;
            }
            if ($isQuoteNode(element)) {
                blockType = 'quote';
                return;
            }
            if ($isListNode(element)) {
                blockType = element.getListType() === 'number' ? 'ol' : element.getListType() === 'check' ? 'check' : 'ul';
                return;
            }
            if ($isCodeNode(element)) {
                blockType = 'code';
                return;
            }
            if ($isTableCellNode(element) || type === 'table' || type === 'tablerow') {
                blockType = 'table';
                return;
            }
            element = element.getParent();
        }
    });
    return blockType;
}

/**
 * Returns `true` if every text node within the selection shares the
 * supplied bit-flag in its `format` mask.
 */
export function hasFormatFlag(editor, flag) {
    let has = false;
    editor.getEditorState().read(() => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) return;
        // Empty / collapsed selection — fall back to the format on the
        // anchor node so toggling "Bold" with no text still reflects
        // the would-be style of the next typed character.
        const nodes = selection.isCollapsed()
            ? [selection.anchor.getNode()]
            : selection.getNodes();

        for (const node of nodes) {
            if (!$isTextNode(node)) continue;
            const nodeFormat = node.getFormat();
            const nextFormat = nodeFormat & flag;
            if (nextFormat !== 0) {
                has = true;
                return;
            }
        }
    });
    return has;
}

/**
 * Read the inline style of the current selection, used for font-family /
 * font-size / color / highlight pickers.
 */
export function getActiveInlineStyle(editor, property) {
    let value = '';
    editor.getEditorState().read(() => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) return;

        if (selection.isCollapsed() && selection.style) {
            for (const part of selection.style.split(';')) {
                const [prop, ...rest] = part.split(':');
                if (prop && prop.trim() === property) {
                    value = rest.join(':').trim();
                    return;
                }
            }
        }

        const nodes = selection.isCollapsed()
            ? [selection.anchor.getNode()]
            : selection.getNodes();
        for (const node of nodes) {
            if ($isTextNode(node)) {
                const style = node.getStyle();
                if (!style) continue;
                for (const part of style.split(';')) {
                    const [prop, ...rest] = part.split(':');
                    if (prop && prop.trim() === property) {
                        value = rest.join(':').trim();
                        return;
                    }
                }
            } else if (typeof node.getTextStyle === 'function') {
                const style = node.getTextStyle();
                if (!style) continue;
                for (const part of style.split(';')) {
                    const [prop, ...rest] = part.split(':');
                    if (prop && prop.trim() === property) {
                        value = rest.join(':').trim();
                        return;
                    }
                }
            }
        }
    });
    return value;
}

/**
 * Lexical stores the per-element text-alignment as a small numeric code on
 * `ElementNode.__format` (1 = left, 2 = center, 3 = right, 4 = justify,
 * 0 = default/inherit). Walk up from the current selection and translate
 * it to the canonical alignment name used by the toolbar UI.
 *
 * @returns {'left'|'center'|'right'|'justify'}
 */
export function getActiveAlignment(editor) {
    let align = 'left';
    editor.getEditorState().read(() => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) return;
        const node = selection.anchor.getNode();
        let parent = node.getKey() === 'root' ? node : node.getParent();
        while (parent) {
            const format = typeof parent.getFormat === 'function' ? parent.getFormat() : null;
            if (format === 2) { align = 'center'; return; }
            if (format === 3) { align = 'right'; return; }
            if (format === 4) { align = 'justify'; return; }
            if (format === 1) { align = 'left'; return; }
            parent = parent.getParent();
        }
    });
    return align;
}

/** Detect whether the current selection (collapsed or not) sits inside a link. */
export function isSelectionInsideLink(editor) {
    let inside = false;
    editor.getEditorState().read(() => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) return;
        const node = selection.anchor.getNode();
        let parent = node.getParent();
        while (parent) {
            if ($isLinkNode(parent)) {
                inside = true;
                return;
            }
            parent = parent.getParent();
        }
    });
    return inside;
}

/** Return the URL of the nearest enclosing LinkNode, if any. */
export function getEnclosingLink(editor) {
    let link = null;
    editor.getEditorState().read(() => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) return;
        const node = selection.anchor.getNode();
        let parent = node.getParent();
        while (parent) {
            if ($isLinkNode(parent)) {
                link = parent;
                return;
            }
            parent = parent.getParent();
        }
    });
    return link;
}

/** Document is empty when the root only contains an empty paragraph. */
export function isDocumentEmpty(editor) {
    let empty = false;
    editor.getEditorState().read(() => {
        const root = $getRoot();
        if (root.getChildrenSize() === 0) {
            empty = true;
            return;
        }
        const first = root.getFirstChild();
        empty =
            first !== null &&
            first.getType() === 'paragraph' &&
            first.getTextContent() === '';
    });
    return empty;
}
