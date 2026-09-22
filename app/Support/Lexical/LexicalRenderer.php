<?php

namespace App\Support\Lexical;

use InvalidArgumentException;

/**
 * Server-side Lexical state → HTML renderer.
 *
 * Converts a Lexical editor state tree (decoded JSON) into safe HTML using
 * the same class names the editor's theme emits (`lex-paragraph`, `lex-h1`,
 * `lex-ul`, etc.). The class names already live in
 * `resources/css/lexical-editor.css`, so any rendered output picks up the
 * editor's typography and spacing automatically.
 *
 * The renderer never trusts incoming markup: text node contents are escaped
 * with `htmlspecialchars`; URLs/attributes are run through a small allow-list
 * that strips javascript:, data:, and vbscript: schemes. Unknown node types
 * are skipped silently so a malformed payload never crashes a page render.
 *
 * Supported node types:
 *  - root
 *  - paragraph, heading (h1..h6), quote
 *  - list (bullet / number / check), listitem
 *  - link
 *  - text (with format flags for bold/italic/underline/strike/code/sub/sup)
 *  - linebreak
 *  - image, youtube, horizontalrule, pagebreak
 *  - table, tablerow, tablecell
 *  - code
 *  - collapsible-container, collapsible-title, collapsible-content
 */
class LexicalRenderer
{
    /** Lexical TextFormat flag bits. Keep in sync with the editor. */
    public const FORMAT_BOLD          = 1;        // 1 << 0
    public const FORMAT_ITALIC        = 1 << 1;   // 2
    public const FORMAT_STRIKETHROUGH = 1 << 2;   // 4
    public const FORMAT_UNDERLINE     = 1 << 3;   // 8
    public const FORMAT_CODE          = 1 << 4;   // 16
    public const FORMAT_SUBSCRIPT     = 1 << 5;   // 32
    public const FORMAT_SUPERSCRIPT   = 1 << 6;   // 64
    public const FORMAT_HIGHLIGHT     = 1 << 7;   // 128

    /**
     * Render a Lexical state (or any value carrying a `root` key) to HTML.
     *
     * Accepts:
     *  - already-decoded array with `root` key
     *  - JSON string of the above
     *  - plain text / HTML / legacy plain string (returned escaped)
     *  - null or empty (returns '')
     */
    public function render(mixed $state): string
    {
        $tree = $this->normalise($state);
        if ($tree === null) {
            return '';
        }

        return $this->renderChildren($tree['root']['children'] ?? []);
    }

    /**
     * Render using a `prose`-friendly wrapper so callers can drop the result
     * inside `<article class="prose ...">` blocks.
     */
    public function renderToHtml(mixed $state, string $wrapperClass = 'lex-rendered'): string
    {
        $inner = $this->render($state);
        if ($inner === '') {
            return '';
        }

        return '<div class="' . $this->escape($wrapperClass) . '">' . $inner . '</div>';
    }

    /**
     * Pull just the plain text out of a Lexical state. Mirrors what the
     * PostController / SpeechController already do for the legacy `content`
     * column mirror — used as a safety net when callers still expect a
     * text fallback (excerpts, search snippets, etc.).
     */
    public function toPlainText(mixed $state): string
    {
        $tree = $this->normalise($state);
        if ($tree === null) {
            return '';
        }

        return trim($this->collectText($tree['root']['children'] ?? []));
    }

    // ── Internal helpers ────────────────────────────────────────────────────

    /**
     * Coerce any input (JSON string, array, plain text, null) into a Lexical
     * state array, or `null` if the value can't be parsed as one.
     */
    protected function normalise(mixed $state): ?array
    {
        if ($state === null || $state === '') {
            return null;
        }

        if (is_string($state)) {
            $trim = trim($state);
            if ($trim === '') {
                return null;
            }

            // Try JSON first.
            if ($trim[0] === '{' || $trim[0] === '[') {
                $decoded = json_decode($trim, true);
                if (is_array($decoded) && isset($decoded['root'])) {
                    return $decoded;
                }
            }

            // Anything else (legacy plain text / HTML / partial) is not a
            // Lexical state. Returning `null` lets callers fall back to their
            // own plain-text rendering.
            return null;
        }

        if (is_array($state) && isset($state['root']) && is_array($state['root'])) {
            return $state;
        }

        return null;
    }

    /**
     * Render an array of child nodes by walking each in order.
     */
    protected function renderChildren(array $children): string
    {
        $out = '';
        foreach ($children as $child) {
            if (!is_array($child)) {
                continue;
            }
            $out .= $this->renderNode($child);
        }
        return $out;
    }

    /**
     * Dispatch to a per-type renderer. Unknown types are skipped silently.
     */
    protected function renderNode(array $node): string
    {
        $type = $node['type'] ?? '';

        return match ($type) {
            'paragraph'       => $this->renderParagraph($node),
            'heading'         => $this->renderHeading($node),
            'quote'           => $this->renderQuote($node),
            'list'            => $this->renderList($node),
            'listitem'        => $this->renderListItem($node),
            'link'            => $this->renderLink($node),
            'text'            => $this->renderText($node),
            'linebreak'       => "<br>\n",
            'image'           => $this->renderImage($node),
            'youtube'         => $this->renderYouTube($node),
            'horizontalrule'  => '<hr class="lex-hr">' . "\n",
            'pagebreak'       => $this->renderPageBreak($node),
            'table'           => $this->renderTable($node),
            'tablerow'        => $this->renderTableRow($node),
            'tablecell'       => $this->renderTableCell($node),
            'code'            => $this->renderCode($node),
            'collapsible-container' => $this->renderCollapsibleContainer($node),
            'collapsible-title'     => $this->renderCollapsibleTitle($node),
            'collapsible-content'   => $this->renderCollapsibleContent($node),
            default                => '',
        };
    }

    protected function renderParagraph(array $node): string
    {
        $inner = $this->renderChildren($node['children'] ?? []);
        if ($inner === '') {
            return '';
        }
        return '<p class="lex-paragraph">' . $inner . '</p>' . "\n";
    }

    protected function renderHeading(array $node): string
    {
        $tag = strtolower((string) ($node['tag'] ?? 'h2'));
        if (!preg_match('/^h[1-6]$/', $tag)) {
            $tag = 'h2';
        }
        $class = 'lex-' . $tag;
        $inner = $this->renderChildren($node['children'] ?? []);
        if ($inner === '') {
            return '';
        }
        return '<' . $tag . ' class="' . $class . '">' . $inner . '</' . $tag . '>' . "\n";
    }

    protected function renderQuote(array $node): string
    {
        $inner = $this->renderChildren($node['children'] ?? []);
        if ($inner === '') {
            return '';
        }
        return '<blockquote class="lex-quote">' . $inner . '</blockquote>' . "\n";
    }

    protected function renderList(array $node): string
    {
        $listType = $node['listType'] ?? 'bullet';

        if ($listType === 'number') {
            $tag = 'ol';
            $class = 'lex-ol';
        } elseif ($listType === 'check') {
            // Checklists aren't list-items in the semantic sense; let the
            // children (listitem nodes with checked flag) render themselves.
            $tag = 'ul';
            $class = 'lex-ul';
        } else {
            $tag = 'ul';
            $class = 'lex-ul';
        }

        $start = isset($node['start']) ? (int) $node['start'] : 0;
        $startAttr = ($tag === 'ol' && $start > 1) ? ' start="' . $start . '"' : '';

        $inner = $this->renderChildren($node['children'] ?? []);
        if ($inner === '') {
            return '';
        }
        return '<' . $tag . ' class="' . $class . '"' . $startAttr . '>' . $inner . '</' . $tag . '>' . "\n";
    }

    protected function renderListItem(array $node): string
    {
        $listType = $node['listType']
            ?? $node['parentListType']
            ?? $this->guessParentListType();

        $checked  = $node['value'] ?? null;

        if ($listType === 'check') {
            $isChecked = $checked === 1 || $checked === true || $checked === 'true';
            $class = $isChecked ? 'lex-li lex-li-checked' : 'lex-li lex-li-unchecked';
            $inner = $this->renderChildren($node['children'] ?? []);
            $checkbox = $isChecked ? '☑ ' : '☐ ';
            return '<li class="' . $class . '">' . $checkbox . $inner . '</li>' . "\n";
        }

        $inner = $this->renderChildren($node['children'] ?? []);
        if ($inner === '') {
            return '';
        }
        return '<li class="lex-li">' . $inner . '</li>' . "\n";
    }

    /**
     * Best-effort fallback: when a listitem doesn't carry its own listType
     * hint we can't know if it's a bullet/number/check without walking up
     * the parent. The Lexical exportJSON used to set listType on items, so
     * this only kicks in for hand-crafted payloads. Return bullet by default.
     */
    protected function guessParentListType(): string
    {
        return 'bullet';
    }

    protected function renderLink(array $node): string
    {
        $url = $this->safeUrl((string) ($node['url'] ?? ''));
        if ($url === '') {
            // Link without a usable URL — render children only so we don't
            // produce a stranded `<a>`.
            return $this->renderChildren($node['children'] ?? []);
        }

        $inner = $this->renderChildren($node['children'] ?? []);
        $rel   = $this->escape((string) ($node['rel'] ?? 'noopener noreferrer'));
        $target = $this->escape((string) ($node['target'] ?? '_blank'));
        $title = isset($node['title']) ? ' title="' . $this->escape((string) $node['title']) . '"' : '';

        return '<a class="lex-link" href="' . $url . '" target="' . $target . '" rel="' . $rel . '"' . $title . '>' . $inner . '</a>';
    }

    protected function renderText(array $node): string
    {
        $text  = (string) ($node['text'] ?? '');
        $html  = $this->escape($text);
        $flags = (int) ($node['format'] ?? 0);

        if ($flags === 0) {
            // Preserve line breaks inside plain text by converting newlines
            // to <br>. Lexical exports each line as a separate text node,
            // but legacy payloads and copy/paste from other editors may
            // embed \n inside a single text node.
            return str_replace(["\r\n", "\n", "\r"], '<br>', $html);
        }

        // Wrappers applied inside-out so the CSS cascade stays correct.
        if ($flags & self::FORMAT_SUBSCRIPT) {
            $html = '<sub class="lex-text-subscript">' . $html . '</sub>';
        }
        if ($flags & self::FORMAT_SUPERSCRIPT) {
            $html = '<sup class="lex-text-superscript">' . $html . '</sup>';
        }
        if ($flags & self::FORMAT_CODE) {
            $html = '<code class="lex-text-code">' . $html . '</code>';
        }
        if ($flags & self::FORMAT_HIGHLIGHT) {
            $html = '<mark class="lex-text-highlight">' . $html . '</mark>';
        }
        if ($flags & self::FORMAT_BOLD) {
            $html = '<strong class="lex-text-bold">' . $html . '</strong>';
        }
        if ($flags & self::FORMAT_ITALIC) {
            $html = '<em class="lex-text-italic">' . $html . '</em>';
        }
        if ($flags & self::FORMAT_UNDERLINE) {
            $html = '<span class="lex-text-underline"><u>' . $html . '</u></span>';
        }
        if ($flags & self::FORMAT_STRIKETHROUGH) {
            $html = '<span class="lex-text-strikethrough"><s>' . $html . '</s></span>';
        }

        return $html;
    }

    protected function renderImage(array $node): string
    {
        $src    = $this->safeUrl((string) ($node['src'] ?? ''));
        if ($src === '') {
            return '';
        }
        $alt    = $this->escape((string) ($node['altText'] ?? ''));
        $width  = isset($node['width'])  ? (int) $node['width']  : null;
        $height = isset($node['height']) ? (int) $node['height'] : null;

        $style = '';
        if ($width)  { $style .= 'width:' . $width . 'px;'; }
        if ($height) { $style .= 'height:' . $height . 'px;'; }
        $styleAttr = $style !== '' ? ' style="' . $this->escape($style) . '"' : '';

        return '<figure class="lex-image-wrapper">'
            . '<img class="lex-image" src="' . $src . '" alt="' . $alt . '" loading="lazy"' . $styleAttr . '>'
            . '</figure>' . "\n";
    }

    protected function renderYouTube(array $node): string
    {
        $videoId = (string) ($node['videoId'] ?? '');
        if ($videoId === '') {
            return '';
        }
        $src = 'https://www.youtube-nocookie.com/embed/' . rawurlencode($videoId);

        return '<div class="lex-youtube">'
            . '<iframe src="' . $this->escape($src) . '" frameborder="0" '
            . 'allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" '
            . 'allowfullscreen loading="lazy" '
            . 'sandbox="allow-scripts allow-same-origin allow-presentation allow-popups"></iframe>'
            . '</div>' . "\n";
    }

    protected function renderPageBreak(array $node): string
    {
        return '<div class="lex-page-break" data-lex-page-break="true">'
            . '<div class="lex-page-break__inner">Page Break</div>'
            . '</div>' . "\n";
    }

    protected function renderTable(array $node): string
    {
        $inner = $this->renderChildren($node['children'] ?? []);
        if ($inner === '') {
            return '';
        }
        return '<table class="lex-table">' . $inner . '</table>' . "\n";
    }

    protected function renderTableRow(array $node): string
    {
        $inner = $this->renderChildren($node['children'] ?? []);
        return '<tr class="lex-tr">' . $inner . '</tr>' . "\n";
    }

    protected function renderTableCell(array $node): string
    {
        $tag = !empty($node['headerState']) ? 'th' : 'td';
        $class = $tag === 'th' ? 'lex-th' : 'lex-td';

        $attrs = '';
        if (!empty($node['colSpan']) && (int) $node['colSpan'] > 1) {
            $attrs .= ' colspan="' . (int) $node['colSpan'] . '"';
        }
        if (!empty($node['rowSpan']) && (int) $node['rowSpan'] > 1) {
            $attrs .= ' rowspan="' . (int) $node['rowSpan'] . '"';
        }

        $inner = $this->renderChildren($node['children'] ?? []);
        return '<' . $tag . ' class="' . $class . '"' . $attrs . '>' . $inner . '</' . $tag . '>' . "\n";
    }

    protected function renderCode(array $node): string
    {
        $code = $this->collectText($node['children'] ?? []);
        $lang = $this->escape((string) ($node['language'] ?? ''));
        return '<pre class="lex-code" data-language="' . $lang . '"><code>'
            . $this->escape(rtrim($code, "\n"))
            . '</code></pre>' . "\n";
    }

    protected function renderCollapsibleContainer(array $node): string
    {
        $open  = ($node['open'] ?? true) !== false;
        $inner = $this->renderChildren($node['children'] ?? []);
        return '<details class="lex-collapsible-container' . ($open ? ' lex-collapsible-container--open' : '') . '"'
            . ($open ? ' open' : '') . '>' . $inner . '</details>' . "\n";
    }

    protected function renderCollapsibleTitle(array $node): string
    {
        $inner = $this->renderChildren($node['children'] ?? []);
        return '<summary class="lex-collapsible-title">' . $inner . '</summary>' . "\n";
    }

    protected function renderCollapsibleContent(array $node): string
    {
        $open  = ($node['open'] ?? true) !== false;
        $inner = $this->renderChildren($node['children'] ?? []);
        $cls = 'lex-collapsible-content' . ($open ? ' lex-collapsible-content--open' : '');
        return '<div class="' . $cls . '">' . $inner . '</div>' . "\n";
    }

    // ── Plain-text extraction ───────────────────────────────────────────────

    protected function collectText(array $children): string
    {
        $out = '';
        foreach ($children as $child) {
            if (!is_array($child)) {
                continue;
            }
            $type = $child['type'] ?? '';
            if ($type === 'text') {
                $out .= (string) ($child['text'] ?? '');
            } elseif ($type === 'linebreak') {
                $out .= "\n";
            } elseif (isset($child['children']) && is_array($child['children'])) {
                $piece = $this->collectText($child['children']);
                if ($piece !== '') {
                    // Block-level nodes get a paragraph break in the plain
                    // mirror, mirroring the controller's extractLexicalPlainText.
                    $blockTags = ['paragraph', 'heading', 'quote', 'list', 'listitem', 'code'];
                    if (in_array($type, $blockTags, true)) {
                        $out .= "\n" . $piece . "\n";
                    } else {
                        $out .= $piece;
                    }
                }
            }
        }
        return $out;
    }

    // ── Escaping / sanitisation ─────────────────────────────────────────────

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    /**
     * Allow http(s), mailto, tel, and protocol-relative URLs only. Anything
     * else (javascript:, data:, vbscript:, etc.) is dropped to keep XSS out
     * of admin-authored content.
     */
    protected function safeUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        // Allow fragment-only and relative URLs as-is (they have no scheme).
        if ($url[0] === '/' || $url[0] === '#' || $url[0] === '?') {
            return $this->escape($url);
        }

        $parts = parse_url($url);
        if ($parts === false || !isset($parts['scheme'])) {
            // No scheme at all — treat as relative.
            return $this->escape($url);
        }

        $scheme = strtolower($parts['scheme']);
        $allowed = ['http', 'https', 'mailto', 'tel'];
        if (!in_array($scheme, $allowed, true)) {
            return '';
        }

        return $this->escape($url);
    }
}
