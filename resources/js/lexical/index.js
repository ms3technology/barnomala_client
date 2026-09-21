/**
 * Lexical editor entry point.
 *
 * Mounts a `LexicalEditor` into every element matching
 * `[data-lexical-editor]` on the page. The toolbar host lives in the same
 * wrapper and is wired up by the Blade component (`#lex-toolbar-{id}`).
 *
 * Each editor reads its initial JSON from `data-lexical-initial` (a JSON
 * object) and pushes the latest serialized state into `data-lexical-target`
 * (a hidden input).
 */
import { createLexicalEditor } from './editor.js';
import { buildConfig } from './config.js';

function mountAll() {
    const editors = document.querySelectorAll('[data-lexical-editor]:not([data-lexical-mounted])');
    editors.forEach((editorElement) => {
        try {
            mountOne(editorElement);
        } catch (error) {
            // eslint-disable-next-line no-console
            console.error('[lexical] failed to mount', error);
        }
    });
}

function mountOne(editorElement) {
    const targetSelector = editorElement.dataset.lexicalTarget;
    if (!targetSelector) {
        // eslint-disable-next-line no-console
        console.warn('[lexical-editor] missing data-lexical-target', editorElement);
        return;
    }
    const hiddenInput = document.querySelector(targetSelector);
    if (!hiddenInput) {
        // eslint-disable-next-line no-console
        console.warn('[lexical-editor] hidden input not found for', targetSelector);
        return;
    }

    const wrapper = editorElement.closest('[data-lexical-wrapper]') || editorElement.parentElement;
    const toolbarEl = wrapper?.querySelector('[data-lexical-toolbar]');

    const initial = parseInitial(editorElement.dataset.lexicalInitial);
    const dataset = editorElement.dataset;
    const config = buildConfig(dataset);
    config.initial = initial ?? parseInitial(hiddenInput.value) ?? null;

    // Allow Blade to override `csrf`/`uploadUrl` via data attributes.
    if (dataset.csrf) config.csrf = dataset.csrf;
    if (dataset.uploadUrl) config.uploadUrl = dataset.uploadUrl;

    editorElement.setAttribute('data-lexical-mounted', '1');

    createLexicalEditor(editorElement, toolbarEl, hiddenInput, config);
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

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountAll);
} else {
    mountAll();
}

// Re-scan when Alpine x-show toggles the wrapper visibility after page load.
document.addEventListener('alpine:initialized', () => {
    setTimeout(mountAll, 50);
});
