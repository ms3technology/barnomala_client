/**
 * HorizontalRuleNode — decorator that renders `<hr>` between blocks.
 *
 * Lexical already ships a `HorizontalRuleNode` in the rich-text package for
 * its DOM import path, but registering our own keeps the theme class
 * predictable and avoids surprising coupled behaviour.
 */
import { DecoratorNode } from 'lexical';

export class HorizontalRuleNode extends DecoratorNode {
    static getType() { return 'horizontalrule'; }

    static clone(node) { return new HorizontalRuleNode(node.__key); }

    static importJSON() { return new HorizontalRuleNode(); }

    constructor(key) {
        super(key);
    }

    createDOM() {
        const el = document.createElement('hr');
        el.className = 'lex-hr';
        el.setAttribute('contenteditable', 'false');
        return el;
    }

    updateDOM() { return false; }

    static importDOM() {
        return {
            hr: () => ({
                conversion: () => ({ node: new HorizontalRuleNode() }),
                priority: 0,
            }),
        };
    }

    exportDOM() {
        const el = document.createElement('hr');
        el.className = 'lex-hr';
        return { element: el };
    }

    exportJSON() {
        return {
            type: 'horizontalrule',
            version: 1,
        };
    }

    isInline() { return false; }
}

export function $createHorizontalRuleNode() {
    return new HorizontalRuleNode();
}

export function $isHorizontalRuleNode(node) {
    return node instanceof HorizontalRuleNode;
}
