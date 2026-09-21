/**
 * List toolbar: bulleted, numbered, and check list.
 */
import {
    INSERT_UNORDERED_LIST_COMMAND,
    INSERT_ORDERED_LIST_COMMAND,
} from '@lexical/list';
import { INSERT_CHECK_LIST_COMMAND } from '@lexical/list';
import { createButton } from './shared.js';

export function mountLists(editor, container, config) {
    const ul = createButton({
        icon: 'list-ul',
        title: 'Bulleted list',
        ariaLabel: 'Bulleted list',
        attr: { 'data-toolbar-action': 'list-ul' },
    });
    const ol = createButton({
        icon: 'list-ol',
        title: 'Numbered list',
        ariaLabel: 'Numbered list',
        attr: { 'data-toolbar-action': 'list-ol' },
    });
    const check = createButton({
        icon: 'list-check',
        title: 'Checklist',
        ariaLabel: 'Checklist',
        attr: { 'data-toolbar-action': 'list-check' },
    });

    ul.addEventListener('click', () => {
        editor.dispatchCommand(INSERT_UNORDERED_LIST_COMMAND, undefined);
        editor.focus();
    });
    ol.addEventListener('click', () => {
        editor.dispatchCommand(INSERT_ORDERED_LIST_COMMAND, undefined);
        editor.focus();
    });
    check.addEventListener('click', () => {
        editor.dispatchCommand(INSERT_CHECK_LIST_COMMAND, undefined);
        editor.focus();
    });

    container.appendChild(ul);
    container.appendChild(ol);
    container.appendChild(check);

    return {
        destroy() {
            ul.remove();
            ol.remove();
            check.remove();
        },
    };
}
