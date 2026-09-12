<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Mirrors the commented-out block in
     * 2026_04_11_122903_add_legacy_id_to_galleries_table.php — adds the
     * `legacy_id` column to `galleries` so the WebsiteDefaultsSeeder (and
     * future legacy-data imports) can write a stable external identifier
     * alongside the auto-increment primary key.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('galleries', 'legacy_id')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->unsignedBigInteger('legacy_id')->nullable()->unique()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('galleries', 'legacy_id')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->dropUnique(['legacy_id']);
                $table->dropColumn('legacy_id');
            });
        }
    }
};