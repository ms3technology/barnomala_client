<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->json('content_lexical')->nullable()->after('content');
        });

        DB::table('posts')->orderBy('id')->chunkById(100, function ($posts): void {
            foreach ($posts as $post) {
                if ($post->content_lexical !== null) {
                    continue;
                }

                $paragraphs = array_values(array_filter(preg_split('/\R{2,}/u', trim((string) $post->content)) ?: [], fn ($text) => $text !== ''));
                $children = array_map(fn ($text) => [
                    'children' => [[
                        'detail' => 0, 'format' => 0, 'mode' => 'normal', 'style' => '',
                        'text' => $text, 'type' => 'text', 'version' => 1,
                    ]],
                    'direction' => null, 'format' => '', 'indent' => 0, 'type' => 'paragraph', 'version' => 1,
                ], $paragraphs);

                DB::table('posts')->where('id', $post->id)->update(['content_lexical' => json_encode([
                    'root' => ['children' => $children, 'direction' => null, 'format' => '', 'indent' => 0, 'type' => 'root', 'version' => 1],
                ], JSON_UNESCAPED_UNICODE)]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('content_lexical');
        });
    }
};
