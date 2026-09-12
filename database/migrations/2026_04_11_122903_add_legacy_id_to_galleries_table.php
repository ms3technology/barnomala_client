<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::table('galleries', function (Blueprint $table) {
        //     $table->unsignedBigInteger('legacy_id')->nullable()->unique()->after('id');
        // });

        // Defensive: notices/news were renamed/merged into `posts` in this
        // codebase (see 2026_09_05_130002_migrate_content_to_posts_table). When
        // running against a fresh SQLite copy we must skip silently rather
        // than blow up with "no such table".
        if (Schema::hasTable('notices')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->unsignedBigInteger('legacy_id')->nullable()->unique()->after('id');
            });
        }

        if (Schema::hasTable('news')) {
            Schema::table('news', function (Blueprint $table) {
                $table->unsignedBigInteger('legacy_id')->nullable()->unique()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn('legacy_id');
        });

        if (Schema::hasTable('notices')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->dropColumn('legacy_id');
            });
        }

        if (Schema::hasTable('news')) {
            Schema::table('news', function (Blueprint $table) {
                $table->dropColumn('legacy_id');
            });
        }
    }
};
