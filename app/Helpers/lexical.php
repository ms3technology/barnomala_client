<?php

use App\Support\Lexical\LexicalRenderer;

if (! function_exists('lexical_render')) {
    /**
     * Render a Lexical editor state (array, JSON string, or legacy plain
     * text) to safe HTML. Returns an empty string for null/empty input.
     *
     * Use this from Blade when displaying rich content produced by the
     * Lexical editor — it walks the JSON tree, escapes text nodes, and
     * produces HTML that matches the `lex-*` classes defined in
     * `resources/css/lexical-editor.css`.
     *
     * @param  mixed   $state
     * @param  string  $fallback  HTML returned when the input is not a
     *                            Lexical state (legacy plain text / HTML).
     * @param  bool    $wrapper   Wrap the result in the `.lex-rendered` div.
     * @return string
     */
    function lexical_render(mixed $state, string $fallback = '', bool $wrapper = false): string
    {
        $renderer = app(LexicalRenderer::class);

        // Empty / null → empty string.
        if ($state === null || $state === '') {
            return '';
        }

        // Array / JSON-string Lexical state → render with the renderer.
        if (is_array($state) || (is_string($state) && $state !== '' && ($state[0] === '{' || $state[0] === '['))) {
            $html = $renderer->render($state);
            if ($html !== '') {
                return $wrapper ? '<div class="lex-rendered">' . $html . '</div>' : $html;
            }
        }

        // Anything else (legacy plain text / HTML) → caller-supplied fallback.
        return $fallback;
    }
}

if (! function_exists('lexical_plain_text')) {
    /**
     * Extract plain text from a Lexical state. Used for excerpts, search
     * snippets, and other places where rich text isn't appropriate.
     *
     * @param  mixed  $state
     * @return string
     */
    function lexical_plain_text(mixed $state): string
    {
        return app(LexicalRenderer::class)->toPlainText($state);
    }
}

if (! function_exists('lexical_is_state')) {
    /**
     * Return true when the value looks like a Lexical editor state (either
     * an array with a `root` key or a JSON string that decodes to one).
     *
     * @param  mixed  $state
     * @return bool
     */
    function lexical_is_state(mixed $state): bool
    {
        if (is_array($state)) {
            return isset($state['root']) && is_array($state['root']);
        }

        if (is_string($state)) {
            $trim = trim($state);
            if ($trim === '' || ($trim[0] !== '{' && $trim[0] !== '[')) {
                return false;
            }
            $decoded = json_decode($trim, true);
            return is_array($decoded) && isset($decoded['root']) && is_array($decoded['root']);
        }

        return false;
    }
}
