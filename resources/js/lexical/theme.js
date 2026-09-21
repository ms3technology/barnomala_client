/**
 * Centralized Lexical theme.
 *
 * Every class referenced here must exist in `resources/css/lexical-editor.css`.
 * Keeping the mapping in one file makes it trivial to restyle the editor
 * without touching the JavaScript that emits markup.
 */
export const theme = {
    // ── Block nodes ──────────────────────────────────────────────────────────
    paragraph: 'lex-paragraph',
    heading: {
        h1: 'lex-h1',
        h2: 'lex-h2',
        h3: 'lex-h3',
        h4: 'lex-h4',
        h5: 'lex-h5',
        h6: 'lex-h6',
    },
    quote: 'lex-quote',
    list: {
        ul: 'lex-ul',
        ol: 'lex-ol',
        listitem: 'lex-li',
        listitemChecked: 'lex-li-checked',
        listitemUnchecked: 'lex-li-unchecked',
        nested: {
            list: 'lex-nested-list',
        },
    },
    table: 'lex-table',
    tableRow: 'lex-tr',
    tableCell: 'lex-td',
    tableCellHeader: 'lex-th',
    code: 'lex-code',
    codeHighlight: {
        atrule: 'lex-token-atrule',
        attr: 'lex-token-attr',
        boolean: 'lex-token-boolean',
        builtin: 'lex-token-builtin',
        cdata: 'lex-token-cdata',
        char: 'lex-token-char',
        class: 'lex-token-class',
        'class-name': 'lex-token-class-name',
        comment: 'lex-token-comment',
        constant: 'lex-token-constant',
        deleted: 'lex-token-deleted',
        doctype: 'lex-token-doctype',
        entity: 'lex-token-entity',
        function: 'lex-token-function',
        important: 'lex-token-important',
        inserted: 'lex-token-inserted',
        keyword: 'lex-token-keyword',
        namespace: 'lex-token-namespace',
        number: 'lex-token-number',
        operator: 'lex-token-operator',
        prolog: 'lex-token-prolog',
        property: 'lex-token-property',
        punctuation: 'lex-token-punctuation',
        regex: 'lex-token-regex',
        selector: 'lex-token-selector',
        string: 'lex-token-string',
        symbol: 'lex-token-symbol',
        tag: 'lex-token-tag',
        url: 'lex-token-url',
        variable: 'lex-token-variable',
    },

    // ── Inline / text nodes ──────────────────────────────────────────────────
    text: {
        bold: 'lex-text-bold',
        italic: 'lex-text-italic',
        underline: 'lex-text-underline',
        strikethrough: 'lex-text-strikethrough',
        code: 'lex-text-code',
        subscript: 'lex-text-subscript',
        superscript: 'lex-text-superscript',
        highlight: 'lex-text-highlight',
    },
    link: 'lex-link',
    hashtag: 'lex-hashtag',

    // ── Decorators ───────────────────────────────────────────────────────────
    image: 'lex-image',
    horizontalRule: 'lex-hr',
    pageBreak: 'lex-page-break',
    collapsible: {
        container: 'lex-collapsible-container',
        title: 'lex-collapsible-title',
        content: 'lex-collapsible-content',
        contentOpen: 'lex-collapsible-content--open',
    },
    youtube: 'lex-youtube',
    date: 'lex-date',

    // ── Misc ─────────────────────────────────────────────────────────────────
    placeholder: 'lex-placeholder',
    mark: 'lex-mark',
    specialText: 'lex-special-text',
};

export default theme;
