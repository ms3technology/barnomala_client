import {
    createEditor,
    FORMAT_TEXT_COMMAND,
    $getRoot,
    $createParagraphNode,
    $createTextNode,
    $getSelection,
    $isRangeSelection,
    FORMAT_ELEMENT_COMMAND,
    IS_BOLD,
    IS_ITALIC,
    IS_UNDERLINE,
    IS_STRIKETHROUGH,
    IS_CODE,
} from 'lexical';

import {
    HeadingNode,
    QuoteNode,
    registerRichText,
} from '@lexical/rich-text';
import {
    registerHistory,
    createEmptyHistoryState,
} from '@lexical/history';

import {
    ListNode,
    ListItemNode,
} from '@lexical/list';
import { LinkNode } from '@lexical/link';

/**
 * Format bit flags used by Lexical's text nodes. Kept locally so we can
 * inspect the active selection without importing the full TextFormatType
 * enum (which differs across versions).
 */
const TEXT_FORMAT_BITS = {
    bold: IS_BOLD,
    italic: IS_ITALIC,
    underline: IS_UNDERLINE,
    strikethrough: IS_STRIKETHROUGH,
    code: IS_CODE,
};

/**
 * Mount a Lexical rich-text editor inside every node matching
 * `[data-lexical-editor]`. Each editor syncs its serialized JSON state
 * into the hidden input selected by `data-lexical-target`, so a Laravel
 * form can submit it as a normal field.
 *
 * Optional attributes:
 *   data-lexical-initial='{"root":{"children":[...]}}'
 *   data-lexical-toolbar='[data-command]'
 *
 * The toolbar element is queried *inside* the same wrapper, so multiple
 * editors on one page stay independent.
 */
function mountEditors() {
    const editors = document.querySelectorAll('[data-lexical-editor]');

    editors.forEach((editorElement) => {
        const targetSelector = editorElement.dataset.lexicalTarget;
        if (!targetSelector) {
            console.warn('[lexical-editor] missing data-lexical-target', editorElement);
            return;
        }

        const hiddenInput = document.querySelector(targetSelector);
        if (!hiddenInput) {
            console.warn('[lexical-editor] hidden input not found for', targetSelector);
            return;
        }

        const initial = parseInitial(editorElement.dataset.lexicalInitial);

        const config = {
            namespace: 'LaravelLexicalEditor',
            nodes: [HeadingNode, QuoteNode, ListNode, ListItemNode, LinkNode],
            onError(error) {
                console.error('[lexical-editor]', error);
            },
            theme: {
                paragraph: 'lexical-paragraph',
                heading: {
                    h1: 'lexical-h1',
                    h2: 'lexical-h2',
                    h3: 'lexical-h3',
                },
                list: {
                    ul: 'lexical-ul',
                    ol: 'lexical-ol',
                    listitem: 'lexical-li',
                },
                link: 'lexical-link',
                quote: 'lexical-quote',
                text: {
                    bold: 'lexical-bold',
                    italic: 'lexical-italic',
                    underline: 'lexical-underline',
                    strikethrough: 'lexical-strikethrough',
                    code: 'lexical-code',
                },
            },
        };

        const editor = createEditor(config);

        // Plain-text + rich-text editing support (registerHistory + basic
        // rich-text nodes). Equivalent to the older all-in-one helper.
        registerRichText(editor);
        registerHistory(editor, createEmptyHistoryState(), 300);

        editor.setRootElement(editorElement);

        // Initial state: from data attribute, or fall back to whatever the
        // hidden input already has (lets us hydrate on the "edit" screen).
        const seed = initial ?? parseInitial(hiddenInput.value);
        if (seed) {
            const parsed = editor.parseEditorState(JSON.stringify(seed));
            editor.setEditorState(parsed);
        } else {
            editor.update(() => {
                const root = $getRoot();
                if (root.isEmpty()) {
                    root.append($createParagraphNode().append($createTextNode('')));
                }
            });
        }

        // Push the latest JSON into the hidden input on every change.
        editor.registerUpdateListener(({ editorState }) => {
            hiddenInput.value = JSON.stringify(editorState.toJSON());
        });

        // Wire up the toolbar scoped to this editor instance.
        const wrapper = editorElement.closest('[data-lexical-wrapper]') ?? editorElement.parentElement;
        const toolbarSelector = editorElement.dataset.lexicalToolbar;
        if (wrapper && toolbarSelector) {
            const buttons = Array.from(wrapper.querySelectorAll(toolbarSelector));
            buttons.forEach((button) => {
                // Keep the editor's selection alive while clicking toolbar
                // buttons. Without `preventDefault()` on `mousedown`, the
                // contenteditable blurs, the selection collapses, and
                // FORMAT_TEXT_COMMAND runs against a detached selection —
                // which is why underline/bold appeared to "not save".
                button.addEventListener('mousedown', (event) => {
                    event.preventDefault();
                });
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    handleCommand(editor, button.dataset.command, button);
                    // Restore focus so subsequent typing lands in the editor.
                    editor.focus();
                });
            });

            // Reflect the current selection's active formats on the buttons.
            editor.registerUpdateListener(() => {
                editor.getEditorState().read(() => {
                    const selection = $getSelection();
                    if (!$isRangeSelection(selection)) {
                        buttons.forEach((b) => b.classList.remove('is-active'));
                        return;
                    }
                    const format = selection.format;
                    buttons.forEach((b) => {
                        const command = b.dataset.command;
                        const bit = TEXT_FORMAT_BITS[command];
                        b.classList.toggle('is-active', bit ? (format & bit) !== 0 : false);
                    });
                });
            });
        }
    });
}

function parseInitial(raw) {
    if (!raw) return null;
    try {
        return JSON.parse(raw);
    } catch (error) {
        console.warn('[lexical-editor] invalid initial JSON', error);
        return null;
    }
}

function handleCommand(editor, command, button) {
    if (!command) return;

    const map = {
        bold: 'bold',
        italic: 'italic',
        underline: 'underline',
        strikethrough: 'strikethrough',
        code: 'code',
        h1: 'h1',
        h2: 'h2',
        h3: 'h3',
        paragraph: 'paragraph',
        quote: 'quote',
        ul: 'ul',
        ol: 'ol',
    };

    const target = map[command];
    if (!target) return;

    if (['bold', 'italic', 'underline', 'strikethrough', 'code'].includes(target)) {
        editor.dispatchCommand(FORMAT_TEXT_COMMAND, target);
        return;
    }

    if (['h1', 'h2', 'h3', 'paragraph', 'quote', 'ul', 'ol'].includes(target)) {
        editor.dispatchCommand(FORMAT_ELEMENT_COMMAND, target);
        return;
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountEditors);
} else {
    mountEditors();
}
