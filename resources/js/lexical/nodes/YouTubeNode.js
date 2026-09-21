/**
 * YouTubeNode — embed YouTube videos via the privacy-enhanced nocookie domain.
 *
 * Renders inside a sandboxed iframe so a malicious embed URL can never escape
 * to the parent document.
 */
import { DecoratorNode } from 'lexical';

export class YouTubeNode extends DecoratorNode {
    __videoId;

    static getType() { return 'youtube'; }

    static clone(node) { return new YouTubeNode(node.__videoId, node.__key); }

    static importJSON(json) {
        return new YouTubeNode(json.videoId);
    }

    constructor(videoId, key) {
        super(key);
        this.__videoId = videoId || '';
    }

    getVideoId() { return this.__videoId; }
    setVideoId(videoId) { const writable = this.getWritable(); writable.__videoId = videoId; }

    createDOM() {
        const wrapper = document.createElement('div');
        wrapper.className = 'lex-youtube';
        wrapper.setAttribute('contenteditable', 'false');

        const iframe = document.createElement('iframe');
        iframe.src = `https://www.youtube-nocookie.com/embed/${encodeURIComponent(this.__videoId)}`;
        iframe.title = 'YouTube video';
        iframe.setAttribute('frameborder', '0');
        iframe.setAttribute('allow', 'accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
        iframe.setAttribute('allowfullscreen', 'true');
        iframe.setAttribute('loading', 'lazy');
        iframe.setAttribute('sandbox', 'allow-scripts allow-same-origin allow-presentation allow-popups');

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'lex-youtube-remove';
        remove.title = 'Remove video';
        remove.setAttribute('aria-label', 'Remove video');
        remove.textContent = '×';
        remove.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            import('lexical').then(({ $getNearestNodeFromDOMNode }) => {
                const node = $getNearestNodeFromDOMNode(remove);
                if (node) node.remove();
            });
        });

        wrapper.appendChild(iframe);
        wrapper.appendChild(remove);
        return wrapper;
    }

    updateDOM(prevNode, dom) {
        if (prevNode.__videoId !== this.__videoId) {
            const iframe = dom.querySelector('iframe');
            if (iframe) iframe.src = `https://www.youtube-nocookie.com/embed/${encodeURIComponent(this.__videoId)}`;
        }
        return false;
    }

    static importDOM() {
        return {
            iframe: (domNode) => {
                const src = domNode.getAttribute('src') || '';
                if (!src.includes('youtube')) return null;
                return {
                    conversion: () => ({ node: new YouTubeNode('') }),
                    priority: 1,
                };
            },
        };
    }

    exportDOM() {
        const wrapper = document.createElement('div');
        wrapper.className = 'lex-youtube';
        wrapper.setAttribute('contenteditable', 'false');
        const iframe = document.createElement('iframe');
        iframe.src = `https://www.youtube-nocookie.com/embed/${encodeURIComponent(this.__videoId)}`;
        iframe.setAttribute('frameborder', '0');
        iframe.setAttribute('allowfullscreen', 'true');
        iframe.setAttribute('sandbox', 'allow-scripts allow-same-origin allow-presentation allow-popups');
        wrapper.appendChild(iframe);
        return { element: wrapper };
    }

    exportJSON() {
        return { type: 'youtube', version: 1, videoId: this.__videoId };
    }

    isInline() { return false; }
}

export function $createYouTubeNode(videoId) {
    return new YouTubeNode(videoId);
}

export function $isYouTubeNode(node) {
    return node instanceof YouTubeNode;
}
