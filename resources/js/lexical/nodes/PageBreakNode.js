/**
 * PageBreakNode — visual + print page break.
 *
 * Behaves like a horizontal rule on screen, but on `@media print` it forces
 * the next content onto a new page. A small dashed bar with the label
 * "Page Break" gives a visual cue while editing.
 */
import { DecoratorNode } from 'lexical';

export class PageBreakNode extends DecoratorNode {
    static getType() { return 'pagebreak'; }

    static clone(node) { return new PageBreakNode(node.__key); }

    static importJSON() { return new PageBreakNode(); }

    constructor(key) {
        super(key);
    }

    createDOM() {
        const wrapper = document.createElement('div');
        wrapper.className = 'lex-page-break';
        wrapper.setAttribute('contenteditable', 'false');
        wrapper.setAttribute('data-lex-page-break', 'true');

        const inner = document.createElement('div');
        inner.className = 'lex-page-break__inner';
        inner.textContent = 'Page Break';

        wrapper.appendChild(inner);
        return wrapper;
    }

    updateDOM() { return false; }

    static importDOM() {
        return {
            div: () => ({
                conversion: (domNode) => {
                    if (domNode.getAttribute && domNode.getAttribute('data-lex-page-break') === 'true') {
                        return { node: new PageBreakNode() };
                    }
                    return { node: null };
                },
                priority: 1,
            }),
        };
    }

    exportDOM() {
        const wrapper = document.createElement('div');
        wrapper.className = 'lex-page-break';
        wrapper.setAttribute('data-lex-page-break', 'true');

        const inner = document.createElement('div');
        inner.className = 'lex-page-break__inner';
        inner.textContent = 'Page Break';

        wrapper.appendChild(inner);
        return { element: wrapper };
    }

    exportJSON() {
        return { type: 'pagebreak', version: 1 };
    }

    isInline() { return false; }
}

export function $createPageBreakNode() {
    return new PageBreakNode();
}

export function $isPageBreakNode(node) {
    return node instanceof PageBreakNode;
}
