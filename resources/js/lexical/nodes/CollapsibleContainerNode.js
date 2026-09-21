/**
 * CollapsibleContainer / CollapsibleTitle / CollapsibleContent nodes.
 *
 * The container holds exactly two children: a `CollapsibleTitleNode` and a
 * `CollapsibleContentNode`. The title row shows a clickable chevron that
 * toggles `__open`. Content children are wrapped in their own element so
 * CSS can hide them when collapsed.
 */
import { ElementNode, $applyNodeReplacement } from 'lexical';

export class CollapsibleTitleNode extends ElementNode {
    static getType() { return 'collapsible-title'; }

    static clone(node) { return new CollapsibleTitleNode(node.__key); }

    static importJSON() { return new CollapsibleTitleNode(); }

    constructor(key) {
        super(key);
    }

    createDOM() {
        const dom = document.createElement('div');
        dom.className = 'lex-collapsible-title';
        dom.setAttribute('contenteditable', 'false');
        return dom;
    }

    updateDOM() { return false; }

    static importDOM() {
        return {
            div: (domNode) => {
                if (!domNode.classList.contains('lex-collapsible-title')) return null;
                return {
                    conversion: () => ({ node: new CollapsibleTitleNode() }),
                    priority: 1,
                };
            },
        };
    }

    exportDOM() {
        const dom = document.createElement('div');
        dom.className = 'lex-collapsible-title';
        dom.setAttribute('contenteditable', 'false');
        return { element: dom };
    }

    exportJSON() {
        return { type: 'collapsible-title', version: 1 };
    }
}

export function $createCollapsibleTitleNode() {
    return $applyNodeReplacement(new CollapsibleTitleNode());
}

export function $isCollapsibleTitleNode(node) {
    return node instanceof CollapsibleTitleNode;
}

export class CollapsibleContentNode extends ElementNode {
    __open;

    static getType() { return 'collapsible-content'; }

    static clone(node) {
        return new CollapsibleContentNode(node.__open, node.__key);
    }

    static importJSON(json) {
        return new CollapsibleContentNode(json.open !== false);
    }

    constructor(open = true, key) {
        super(key);
        this.__open = open !== false;
    }

    isOpen() { return this.__open; }
    setOpen(open) { const writable = this.getWritable(); writable.__open = !!open; return writable; }

    createDOM() {
        const dom = document.createElement('div');
        dom.className = 'lex-collapsible-content';
        if (this.__open) dom.classList.add('lex-collapsible-content--open');
        return dom;
    }

    updateDOM(prevNode, dom) {
        if (prevNode.__open !== this.__open) {
            dom.classList.toggle('lex-collapsible-content--open', this.__open);
        }
        return false;
    }

    static importDOM() {
        return {
            div: (domNode) => {
                if (!domNode.classList.contains('lex-collapsible-content')) return null;
                return {
                    conversion: () => ({ node: new CollapsibleContentNode(true) }),
                    priority: 1,
                };
            },
        };
    }

    exportDOM() {
        const dom = document.createElement('div');
        dom.className = 'lex-collapsible-content lex-collapsible-content--open';
        return { element: dom };
    }

    exportJSON() {
        return { type: 'collapsible-content', version: 1, open: this.__open };
    }
}

export function $createCollapsibleContentNode(open = true) {
    return $applyNodeReplacement(new CollapsibleContentNode(open));
}

export function $isCollapsibleContentNode(node) {
    return node instanceof CollapsibleContentNode;
}

export class CollapsibleContainerNode extends ElementNode {
    __open;

    static getType() { return 'collapsible-container'; }

    static clone(node) {
        return new CollapsibleContainerNode(node.__open, node.__key);
    }

    static importJSON(json) {
        return new CollapsibleContainerNode(json.open !== false);
    }

    constructor(open = true, key) {
        super(key);
        this.__open = open !== false;
    }

    isOpen() { return this.__open; }
    setOpen(open) { const writable = this.getWritable(); writable.__open = !!open; return writable; }

    createDOM() {
        const dom = document.createElement('details');
        dom.className = 'lex-collapsible-container';
        if (this.__open) dom.setAttribute('open', 'open');
        return dom;
    }

    updateDOM(prevNode, dom) {
        if (prevNode.__open !== this.__open) {
            dom.classList.toggle('lex-collapsible-container--open', this.__open);
            if (this.__open) dom.setAttribute('open', 'open');
            else dom.removeAttribute('open');
        }
        return false;
    }

    static importDOM() {
        return {
            details: () => ({
                conversion: () => ({ node: new CollapsibleContainerNode(true) }),
                priority: 0,
            }),
        };
    }

    exportDOM() {
        const dom = document.createElement('details');
        dom.className = 'lex-collapsible-container';
        if (this.__open) dom.setAttribute('open', 'open');
        return { element: dom };
    }

    exportJSON() {
        return { type: 'collapsible-container', version: 1, open: this.__open };
    }
}

export function $createCollapsibleContainerNode(open = true) {
    return $applyNodeReplacement(new CollapsibleContainerNode(open));
}

export function $isCollapsibleContainerNode(node) {
    return node instanceof CollapsibleContainerNode;
}
