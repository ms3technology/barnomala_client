/**
 * ImageNode — standalone image decorator with src / altText / width / height.
 *
 * Implementing our own node instead of the `@lexical/rich-text` version lets
 * us keep the URL-based payload (no base64) and gate the upload through the
 * Laravel endpoint. The DOM wrapper has a small "remove" button that
 * removes the node when clicked.
 */
import { DecoratorNode } from 'lexical';
import { findAncestorAttr, removeByKey } from '../utils/dom.js';

export class ImageNode extends DecoratorNode {
    __src;
    __altText;
    __width;
    __height;

    static getType() { return 'image'; }

    static clone(node) {
        return new ImageNode(node.__src, node.__altText, node.__width, node.__height, node.__key);
    }

    static importJSON(json) {
        return new ImageNode(json.src, json.altText || '', json.width || null, json.height || null);
    }

    constructor(src, altText, width, height, key) {
        super(key);
        this.__src = src;
        this.__altText = altText || '';
        this.__width = width || null;
        this.__height = height || null;
    }

    getSrc() { return this.__src; }
    getAltText() { return this.__altText; }
    getWidth() { return this.__width; }
    getHeight() { return this.__height; }

    setSrc(src) { const writable = this.getWritable(); writable.__src = src; }
    setAltText(altText) { const writable = this.getWritable(); writable.__altText = altText; }
    setDimensions({ width, height }) {
        const writable = this.getWritable();
        writable.__width = width;
        writable.__height = height;
    }

    createDOM() {
        const wrapper = document.createElement('span');
        wrapper.className = 'lex-image-wrapper';
        wrapper.setAttribute('contenteditable', 'false');
        wrapper.setAttribute('data-lexical-node-key', this.__key);

        const img = document.createElement('img');
        img.className = 'lex-image';
        img.src = this.__src;
        img.alt = this.__altText;
        img.draggable = false;
        if (this.__width) img.style.width = this.__width + 'px';
        if (this.__height) img.style.height = this.__height + 'px';

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'lex-image-remove';
        remove.title = 'Remove image';
        remove.setAttribute('aria-label', 'Remove image');
        remove.textContent = '×';
        remove.addEventListener('click', (event) => {
            const key = findAncestorAttr(remove, 'data-lexical-node-key');
            removeByKey(event, key);
        });

        wrapper.appendChild(img);
        wrapper.appendChild(remove);
        return wrapper;
    }

    updateDOM(prevNode, dom) {
        if (prevNode.__src !== this.__src) {
            const img = dom.querySelector('img');
            if (img) img.src = this.__src;
        }
        if (prevNode.__altText !== this.__altText) {
            const img = dom.querySelector('img');
            if (img) img.alt = this.__altText;
        }
        if (prevNode.__width !== this.__width || prevNode.__height !== this.__height) {
            const img = dom.querySelector('img');
            if (img) {
                if (this.__width) img.style.width = this.__width + 'px';
                else img.style.removeProperty('width');
                if (this.__height) img.style.height = this.__height + 'px';
                else img.style.removeProperty('height');
            }
        }
        return false;
    }

    static importDOM() {
        return {
            img: () => ({
                conversion: (domNode) => {
                    const src = domNode.getAttribute('src') || '';
                    if (!src || src.startsWith('data:')) return { node: null };
                    return {
                        node: new ImageNode(src, domNode.getAttribute('alt') || ''),
                    };
                },
                priority: 0,
            }),
        };
    }

    exportDOM() {
        const wrapper = document.createElement('span');
        wrapper.className = 'lex-image-wrapper';
        wrapper.setAttribute('contenteditable', 'false');

        const img = document.createElement('img');
        img.className = 'lex-image';
        img.src = this.__src;
        img.alt = this.__altText;
        img.draggable = false;
        if (this.__width) img.style.width = this.__width + 'px';
        if (this.__height) img.style.height = this.__height + 'px';

        wrapper.appendChild(img);
        return { element: wrapper };
    }

    exportJSON() {
        return {
            type: 'image',
            version: 1,
            src: this.__src,
            altText: this.__altText,
            width: this.__width,
            height: this.__height,
        };
    }

    isInline() { return false; }
}

export function $createImageNode({ src, altText, width, height } = {}) {
    return new ImageNode(src || '', altText || '', width || null, height || null);
}

export function $isImageNode(node) {
    return node instanceof ImageNode;
}
