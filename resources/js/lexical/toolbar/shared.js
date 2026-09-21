/**
 * Toolbar shared utilities.
 *
 * `createButton`, `createSelect`, `createDivider` etc. produce DOM nodes
 * that follow the playground styling. Each helper accepts a `toolbar`
 * object returned by `Toolbar.js` so we can route keyboard focus and
 * outside-click dismissal consistently.
 */

import { snapshotSelection } from '../utils/focus.js';

export function createButton({ label, title, icon, ariaLabel, onClick, attr = {} } = {}) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'lex-tb-button';
    btn.setAttribute('aria-label', ariaLabel || title || label || '');
    if (title) btn.title = title;

    if (icon) {
        const i = document.createElement('span');
        i.className = `lex-tb-icon lex-tb-icon-${icon}`;
        i.setAttribute('aria-hidden', 'true');
        btn.appendChild(i);
    }
    if (label) {
        const text = document.createElement('span');
        text.className = 'lex-tb-label';
        text.textContent = label;
        btn.appendChild(text);
    }
    for (const [k, v] of Object.entries(attr)) {
        if (v !== undefined && v !== null) btn.setAttribute(k, String(v));
    }

    // Preserve selection on pointer-down so click handlers can dispatch
    // commands against the user's previous selection. Without this the
    // contenteditable blurs as soon as the toolbar receives focus.
    btn.addEventListener('mousedown', (event) => {
        event.preventDefault();
    });

    if (typeof onClick === 'function') {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            onClick(event, btn);
        });
    }
    return btn;
}

export function createSelect({ title, ariaLabel, options, value, onChange, attr = {} } = {}) {
    const select = document.createElement('select');
    select.className = 'lex-tb-select';
    select.title = title || ariaLabel || '';
    select.setAttribute('aria-label', ariaLabel || title || '');
    for (const [k, v] of Object.entries(attr)) {
        if (v !== undefined && v !== null) select.setAttribute(k, String(v));
    }
    for (const opt of options) {
        const el = document.createElement('option');
        el.value = opt.value;
        el.textContent = opt.label;
        if (opt.value === value) el.selected = true;
        select.appendChild(el);
    }
    select.addEventListener('mousedown', (event) => event.preventDefault());
    select.addEventListener('change', (event) => {
        event.preventDefault();
        onChange?.(select.value, select);
    });
    return select;
}

export function createDivider() {
    const sep = document.createElement('span');
    sep.className = 'lex-tb-divider';
    sep.setAttribute('aria-hidden', 'true');
    return sep;
}

/**
 * Build a popover-style dropdown (button + content container).
 *
 * @param {object} options
 * @param {string} options.label
 * @param {string} [options.icon]
 * @param {string} [options.title]
 * @param {HTMLElement} options.content
 * @param {(open:boolean)=>void} [options.onToggle]
 */
export function createDropdown({ label, icon, title, content, onToggle }) {
    const wrap = document.createElement('div');
    wrap.className = 'lex-tb-dropdown';

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'lex-tb-button lex-tb-dropdown__toggle';
    button.title = title || label;
    button.setAttribute('aria-haspopup', 'true');
    button.setAttribute('aria-expanded', 'false');
    button.setAttribute('aria-label', title || label);
    if (icon) {
        const i = document.createElement('span');
        i.className = `lex-tb-icon lex-tb-icon-${icon}`;
        i.setAttribute('aria-hidden', 'true');
        button.appendChild(i);
    }
    if (label) {
        const t = document.createElement('span');
        t.className = 'lex-tb-label';
        t.textContent = label;
        button.appendChild(t);
    }
    const caret = document.createElement('span');
    caret.className = 'lex-tb-caret';
    caret.setAttribute('aria-hidden', 'true');
    caret.textContent = '▾';
    button.appendChild(caret);

    content.classList.add('lex-tb-dropdown__content');
    content.style.display = 'none';

    let open = false;
    let restore = null;
    const close = () => {
        if (!open) return;
        open = false;
        content.style.display = 'none';
        button.setAttribute('aria-expanded', 'false');
        wrap.classList.remove('lex-tb-dropdown--open');
        document.removeEventListener('mousedown', onDocClick, true);
        document.removeEventListener('keydown', onKeyDown, true);
        onToggle?.(false);
    };
    const onDocClick = (event) => {
        if (!wrap.contains(event.target)) close();
    };
    const onKeyDown = (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            close();
            button.focus();
        }
    };
    const toggle = (event) => {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        if (open) {
            close();
            return;
        }
        // Snapshot the editor selection while we're still inside the
        // current selection context, so the click that opens the dropdown
        // doesn't blow it away.
        restore = snapshotSelection(window.__lexicalActiveEditor);
        open = true;
        content.style.display = '';
        button.setAttribute('aria-expanded', 'true');
        wrap.classList.add('lex-tb-dropdown--open');
        document.addEventListener('mousedown', onDocClick, true);
        document.addEventListener('keydown', onKeyDown, true);
        onToggle?.(true);
    };

    button.addEventListener('mousedown', (event) => event.preventDefault());
    button.addEventListener('click', toggle);

    wrap.appendChild(button);
    wrap.appendChild(content);

    return {
        root: wrap,
        open: () => { if (!open) toggle(); },
        close,
        isOpen: () => open,
        /** Restore the editor selection. Call from inside an editor click handler. */
        restoreSelection: () => {
            if (restore) {
                restore();
                restore = null;
            }
        },
    };
}
