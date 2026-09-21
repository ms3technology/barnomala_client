/**
 * Editor bootstrap.
 *
 * Creates a configured `LexicalEditor` instance, registers the right
 * plugins/nodes for our feature set, and returns helpers that the toolbar
 * uses to drive state. Keeping this file thin means the toolbar modules
 * only depend on `createLexicalEditor` — they don't need to know which
 * plugins are wired.
 */
import {
    createEditor,
    createEmptyHistoryState,
    registerHistory,
    $getSelection,
    $isRangeSelection,
    $createParagraphNode,
    $createTextNode,
} from 'lexical';
import {
    HeadingNode,
    QuoteNode,
} from '@lexical/rich-text';
import {
    ListNode,
    ListItemNode,
    registerList,
    registerCheckList,
    INSERT_CHECK_LIST_COMMAND,
    $createListNode,
    $createListItemNode,
} from '@lexical/list';
import { LinkNode } from '@lexical/link';
import {
    TableNode,
    TableRowNode,
    TableCellNode,
    registerTablePlugin,
    registerTableSelectionObserver,
    $createTableNodeWithDimensions,
    INSERT_TABLE_COMMAND,
} from '@lexical/table';
import { CodeNode, CodeHighlightNode, $createCodeNode } from '@lexical/code';
import { registerMarkdownShortcuts, TRANSFORMERS } from '@lexical/markdown';

import { theme } from './theme.js';
import { HorizontalRuleNode, $createHorizontalRuleNode } from './nodes/HorizontalRuleNode.js';
import { PageBreakNode, $createPageBreakNode } from './nodes/PageBreakNode.js';
import { ImageNode, $createImageNode } from './nodes/ImageNode.js';
import {
    CollapsibleContainerNode,
    CollapsibleTitleNode,
    CollapsibleContentNode,
    $createCollapsibleContainerNode,
    $createCollapsibleTitleNode,
    $createCollapsibleContentNode,
} from './nodes/CollapsibleContainerNode.js';
import { YouTubeNode, $createYouTubeNode } from './nodes/YouTubeNode.js';
import {
    INSERT_HORIZONTAL_RULE_COMMAND,
    INSERT_PAGE_BREAK_COMMAND,
    INSERT_IMAGE_COMMAND,
    INSERT_YOUTUBE_COMMAND,
    INSERT_COLLAPSIBLE_COMMAND,
    INSERT_CODE_BLOCK_COMMAND,
    INSERT_DATE_COMMAND,
} from './commands/registry.js';
import { mountToolbar } from './toolbar/Toolbar.js';
import { registerUploadHandler } from './plugins/UploadPlugin.js';

/**
 * Build a configured editor and bind it to a contenteditable element.
 *
 * @param {HTMLElement} rootEl
 * @param {HTMLElement} toolbarEl
 * @param {HTMLElement} hiddenInput
 * @param {object} config
 */
export function createLexicalEditor(rootEl, toolbarEl, hiddenInput, config) {
    const nodes = [
        HeadingNode,
        QuoteNode,
        ListNode,
        ListItemNode,
        LinkNode,
        TableNode,
        TableRowNode,
        TableCellNode,
        CodeNode,
        CodeHighlightNode,
        HorizontalRuleNode,
        PageBreakNode,
        ImageNode,
        CollapsibleContainerNode,
        CollapsibleTitleNode,
        CollapsibleContentNode,
        YouTubeNode,
    ];

    const editor = createEditor({
        namespace: 'LaravelLexicalEditor',
        nodes,
        onError(error) {
            // eslint-disable-next-line no-console
            console.error('[lexical]', error);
        },
        theme,
    });

    // ── Register plugins ────────────────────────────────────────────────────
    registerHistory(editor, createEmptyHistoryState(), 300);
    registerList(editor);
    registerCheckList(editor);
    registerTablePlugin(editor);
    registerTableSelectionObserver(editor, false);

    // Markdown shortcuts (#, ##, >, -, *, 1., **, *, _, `, ~~)
    registerMarkdownShortcuts(editor, TRANSFORMERS);

    // Image upload: paste / drag-drop files
    registerUploadHandler(editor, config);

    // ── Commands ────────────────────────────────────────────────────────────
    editor.registerCommand(INSERT_HORIZONTAL_RULE_COMMAND, () => {
        editor.update(() => {
            const selection = $getSelection();
            if ($isRangeSelection(selection)) {
                selection.insertNodes([$createHorizontalRuleNode(), $createParagraphNode()]);
            }
        }, { discrete: true });
        return true;
    }, 1);

    editor.registerCommand(INSERT_PAGE_BREAK_COMMAND, () => {
        editor.update(() => {
            const selection = $getSelection();
            if ($isRangeSelection(selection)) {
                selection.insertNodes([$createPageBreakNode(), $createParagraphNode()]);
            }
        }, { discrete: true });
        return true;
    }, 1);

    editor.registerCommand(INSERT_IMAGE_COMMAND, (payload) => {
        editor.update(() => {
            const selection = $getSelection();
            if (!$isRangeSelection(selection)) return;
            const nodes = [
                $createImageNode({ src: payload.src, altText: payload.altText || '' }),
                $createParagraphNode(),
            ];
            selection.insertNodes(nodes);
        }, { discrete: true });
        return true;
    }, 1);

    editor.registerCommand(INSERT_YOUTUBE_COMMAND, (payload) => {
        editor.update(() => {
            const selection = $getSelection();
            if (!$isRangeSelection(selection)) return;
            selection.insertNodes([$createYouTubeNode(payload.videoId), $createParagraphNode()]);
        }, { discrete: true });
        return true;
    }, 1);

    editor.registerCommand(INSERT_COLLAPSIBLE_COMMAND, () => {
        editor.update(() => {
            const selection = $getSelection();
            if (!$isRangeSelection(selection)) return;
            const container = $createCollapsibleContainerNode(true);
            const title = $createCollapsibleTitleNode();
            const titleParagraph = $createParagraphNode();
            titleParagraph.append($createTextNode('Details'));
            title.append(titleParagraph);
            const content = $createCollapsibleContentNode(true);
            const contentParagraph = $createParagraphNode();
            contentParagraph.append($createTextNode('Hidden content'));
            content.append(contentParagraph);
            container.append(title);
            container.append(content);
            selection.insertNodes([container, $createParagraphNode()]);
        }, { discrete: true });
        return true;
    }, 1);

    editor.registerCommand(INSERT_CODE_BLOCK_COMMAND, (payload) => {
        editor.update(() => {
            const selection = $getSelection();
            if (!$isRangeSelection(selection)) return;
            const node = $createCodeNode(payload.language || 'javascript');
            selection.insertNodes([node, $createParagraphNode()]);
        }, { discrete: true });
        return true;
    }, 1);

    editor.registerCommand(INSERT_DATE_COMMAND, () => {
        editor.update(() => {
            const selection = $getSelection();
            if (!$isRangeSelection(selection)) return;
            selection.insertRawText(` [${new Date().toISOString().slice(0, 10)}] `);
        }, { discrete: true });
        return true;
    }, 1);

    editor.registerCommand(INSERT_TABLE_COMMAND, (payload) => {
        editor.update(() => {
            const selection = $getSelection();
            if (!$isRangeSelection(selection)) return;
            const tableNode = $createTableNodeWithDimensions(
                payload.rows || 3,
                payload.columns || 3,
                true,
            );
            selection.insertNodes([tableNode, $createParagraphNode()]);
        }, { discrete: true });
        return true;
    }, 1);

    editor.registerCommand(INSERT_CHECK_LIST_COMMAND, () => {
        editor.update(() => {
            const selection = $getSelection();
            if (!$isRangeSelection(selection)) return;
            const list = $createListNode('check');
            const item = $createListItemNode();
            list.append(item);
            selection.insertNodes([list, $createParagraphNode()]);
        }, { discrete: true });
        return true;
    }, 1);

    // ── Mount DOM + initial state ───────────────────────────────────────────
    editor.setRootElement(rootEl);

    const initial = parseInitial(config.initial);
    if (initial) {
        try {
            const parsed = editor.parseEditorState(JSON.stringify(initial));
            editor.setEditorState(parsed);
        } catch (error) {
            // eslint-disable-next-line no-console
            console.error('[lexical] failed to parse initial JSON', error);
        }
    }

    // ── Sync serialized JSON into the hidden field ──────────────────────────
    let scheduled = false;
    const writeState = () => {
        if (scheduled) return;
        scheduled = true;
        queueMicrotask(() => {
            scheduled = false;
            try {
                const json = editor.getEditorState().toJSON();
                hiddenInput.value = JSON.stringify(json);
                hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
            } catch (error) {
                // eslint-disable-next-line no-console
                console.error('[lexical] serialize error', error);
            }
        });
    };

    writeState();
    editor.registerUpdateListener(writeState);

    // ── Toolbar ─────────────────────────────────────────────────────────────
    const toolbarHandle = mountToolbar(editor, toolbarEl, config);

    return {
        editor,
        setState(json) {
            if (!json) return;
            try {
                const parsed = editor.parseEditorState(JSON.stringify(json));
                editor.setEditorState(parsed);
            } catch (error) {
                // eslint-disable-next-line no-console
                console.error('[lexical] setState failed', error);
            }
        },
        serialize() {
            return JSON.stringify(editor.getEditorState().toJSON());
        },
        destroy() {
            toolbarHandle.destroy();
        },
    };
}

function parseInitial(raw) {
    if (!raw) return null;
    if (typeof raw === 'object') return raw;
    try {
        return JSON.parse(raw);
    } catch (_) {
        return null;
    }
}
