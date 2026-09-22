<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Convert the `institute.about.text` option from plain text to a Lexical
 * editor state (JSON).
 *
 * The value is stored in the polymorphic `options` table — schema column
 * is already TEXT — so the migration only needs to:
 *
 *  1. Re-serialize the existing value as a Lexical state when it isn't
 *     already one (i.e. when `option_value` is plain text rather than
 *     a `{"root": ...}` document).
 *  2. Flip `value_type` from `string` to `json` so the rest of the
 *     app — `Option::value` accessor, `Option::set()` helper,
 *     `OptionController`'s JSON_OPTION_KEYS list — treats it as structured.
 *
 * Idempotent: rows that are already Lexical state are left untouched,
 * only `value_type` is normalized to `json`.
 *
 * `down()` reverses the conversion: it serializes any text-node leaves
 * back into a single plain-text string with paragraph breaks.
 */
return new class extends Migration
{
    public function up(): void
    {
        $row = DB::table('options')->where('option_key', 'institute.about.text')->first();

        // No row yet — nothing to migrate; the next save will set value_type
        // correctly via the Option model mutator.
        if (!$row) {
            return;
        }

        $current = (string) ($row->option_value ?? '');
        if ($current === '') {
            // Empty value: still flip the type so the form-renderer in
            // admin/options/theme.blade.php sees it as a JSON-backed field.
            DB::table('options')
                ->where('option_key', 'institute.about.text')
                ->update(['value_type' => 'json']);
            return;
        }

        $decoded = json_decode($current, true);
        if (is_array($decoded) && isset($decoded['root']['children']) && is_array($decoded['root']['children'])) {
            // Already a Lexical state — just make sure value_type is correct.
            DB::table('options')
                ->where('option_key', 'institute.about.text')
                ->update([
                    'value_type' => 'json',
                    'option_value' => json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
            return;
        }

        // Plain text → split on blank lines into Lexical paragraphs.
        $paragraphs = array_values(array_filter(
            preg_split('/\R{2,}/u', trim($current)) ?: [],
            static fn (string $text): bool => trim($text) !== ''
        ));

        // Fallback: if the text has no blank lines, treat the whole value
        // as a single paragraph.
        if ($paragraphs === []) {
            $paragraphs = [trim($current)];
        }

        $children = array_map(
            static fn (string $text): array => [
                'children' => [[
                    'detail' => 0,
                    'format' => 0,
                    'mode' => 'normal',
                    'style' => '',
                    'text' => $text,
                    'type' => 'text',
                    'version' => 1,
                ]],
                'direction' => null,
                'format' => '',
                'indent' => 0,
                'type' => 'paragraph',
                'version' => 1,
            ],
            $paragraphs,
        );

        $state = [
            'root' => [
                'children' => $children,
                'direction' => null,
                'format' => '',
                'indent' => 0,
                'type' => 'root',
                'version' => 1,
            ],
        ];

        DB::table('options')
            ->where('option_key', 'institute.about.text')
            ->update([
                'value_type' => 'json',
                'option_value' => json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);
    }

    public function down(): void
    {
        $row = DB::table('options')->where('option_key', 'institute.about.text')->first();
        if (!$row) {
            return;
        }

        $decoded = json_decode((string) $row->option_value, true);
        if (!is_array($decoded) || !isset($decoded['root']['children']) || !is_array($decoded['root']['children'])) {
            // Already plain text — just normalize value_type.
            DB::table('options')
                ->where('option_key', 'institute.about.text')
                ->update(['value_type' => 'string']);
            return;
        }

        // Walk the Lexical tree and re-collect text leaves as a single string.
        $plain = $this->collectText($decoded['root']['children']);

        DB::table('options')
            ->where('option_key', 'institute.about.text')
            ->update([
                'value_type' => 'string',
                'option_value' => $plain,
            ]);
    }

    /**
     * Recursive plain-text extractor that mirrors the one used by
     * PostController / LexicalRenderer::collectText().
     */
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
                if ($piece !== '' && in_array($type, ['paragraph', 'heading', 'quote', 'list', 'listitem', 'code'], true)) {
                    $out .= "\n" . $piece . "\n";
                } else {
                    $out .= $piece;
                }
            }
        }
        return trim($out);
    }
};
