/**
 * Link popover.
 *
 * Three actions: insert link, edit link, remove link. The dropdown is
 * adaptive — when the current selection sits inside a LinkNode it shows
 * the URL field pre-populated with a "Remove" button; otherwise it shows
 * an empty field + "Apply".
 *
 * All URL validation goes through `sanitizeUrl`. Disallowed URLs are
 * rejected with an inline error instead of being persisted.
 */
import { TOGGLE_LINK_COMMAND } from '@lexical/link';
import { $getSelection, $isRangeSelection } from 'lexical';
import { createButton, createDropdown } from './shared.js';
import { sanitizeUrl } from '../utils/sanitize.js';
import { isSelectionInsideLink, getEnclosingLink } from '../utils/format.js';

export function mountLink(editor, container, config) {
    const content = document.createElement('div');
    content.className = 'lex-tb-menu lex-tb-menu--link';

    const label = document.createElement('label');
    label.className = 'lex-tb-link__label';
    label.textContent = 'URL';

    const input = document.createElement('input');
    input.type = 'url';
    input.className = 'lex-tb-link__input';
    input.placeholder = 'https://example.com';
    input.autocomplete = 'off';

    const error = document.createElement('p');
    error.className = 'lex-tb-link__error';
    error.setAttribute('role', 'alert');
    error.hidden = true;

    const applyBtn = createButton({
        label: 'Apply',
        title: 'Insert link',
        attr: { 'data-toolbar-action': 'link-apply' },
    });
    const removeBtn = createButton({
        label: 'Remove',
        title: 'Remove link',
        attr: { 'data-toolbar-action': 'link-remove' },
    });

    applyBtn.style.flex = '1';
    removeBtn.style.flex = '1';

    label.setAttribute('for', 'lex-tb-link-input');
    input.id = 'lex-tb-link-input';

    const actions = document.createElement('div');
    actions.className = 'lex-tb-link__actions';
    actions.appendChild(applyBtn);
    actions.appendChild(removeBtn);

    content.appendChild(label);
    content.appendChild(input);
    content.appendChild(error);
    content.appendChild(actions);

    const dropdown = createDropdown({
        label: '',
        icon: 'link',
        title: 'Insert / edit link',
        content,
    });

    let currentLink = null;

    const refreshState = () => {
        const inside = isSelectionInsideLink(editor);
        currentLink = inside ? getEnclosingLink(editor) : null;
        const button = dropdown.root.querySelector('.lex-tb-dropdown__toggle');
        if (button) {
            button.classList.toggle('is-active', inside);
            button.setAttribute('aria-pressed', inside ? 'true' : 'false');
        }
        if (currentLink) {
            input.value = currentLink.getURL?.() || '';
            removeBtn.hidden = false;
        } else {
            input.value = '';
            removeBtn.hidden = true;
        }
    };

    const validateAndApply = () => {
        const url = sanitizeUrl(input.value);
        if (!url) {
            error.textContent = 'Invalid URL. Use http://, https://, mailto: or tel:';
            error.hidden = false;
            return;
        }
        error.hidden = true;
        editor.dispatchCommand(TOGGLE_LINK_COMMAND, url);
        dropdown.close();
        editor.focus();
    };

    const remove = () => {
        editor.dispatchCommand(TOGGLE_LINK_COMMAND, null);
        dropdown.close();
        editor.focus();
    };

    applyBtn.addEventListener('click', validateAndApply);
    removeBtn.addEventListener('click', remove);
    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            validateAndApply();
        } else if (event.key === 'Escape') {
            event.preventDefault();
            dropdown.close();
        }
    });
    input.addEventListener('input', () => {
        error.hidden = true;
    });

    container.appendChild(dropdown.root);
    const unregister = editor.registerUpdateListener(() => refreshState());
    refreshState();

    return {
        destroy() {
            unregister();
            dropdown.root.remove();
        },
    };
}
