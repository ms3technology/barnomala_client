<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop dependent indexes / unique constraints before removing the
        // columns. Names mirror what Laravel generated from the original
        // schema; explicit names keep this idempotent on MySQL/MariaDB.
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropUnique('posts_source_type_source_id_unique');
                $table->dropIndex('posts_source_type_legacy_id_index');
                $table->dropIndex('posts_type_source_type_is_active_published_at_index');
                $table->dropIndex('posts_type_source_type_is_active_sort_order_index');
            });

            // legacy_id may still hold useful data from the WordPress import,
            // so drop its foreign-key-style reference to source_type by
            // indexing (legacy_id) on its own for lookups in the transfer
            // service.
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn(['source_type', 'source_id']);
                $table->index('legacy_id');
                $table->index(['type', 'is_active', 'published_at']);
                $table->index(['type', 'is_active', 'sort_order']);
            });
        }

        if (Schema::hasTable('post_artifacts')) {
            Schema::table('post_artifacts', function (Blueprint $table) {
                $table->dropUnique('post_artifacts_source_type_source_id_unique');
                $table->dropColumn(['source_type', 'source_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropIndex('posts_legacy_id_index');
                $table->dropIndex('posts_type_is_active_published_at_index');
                $table->dropIndex('posts_type_is_active_sort_order_index');
            });

            Schema::table('posts', function (Blueprint $table) {
                $table->string('source_type', 32)->nullable();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->unique(['source_type', 'source_id']);
                $table->index(['source_type', 'legacy_id']);
                $table->index(['type', 'source_type', 'is_active', 'published_at']);
                $table->index(['type', 'source_type', 'is_active', 'sort_order']);
            });
        }

        if (Schema::hasTable('post_artifacts')) {
            Schema::table('post_artifacts', function (Blueprint $table) {
                $table->string('source_type', 32);
                $table->unsignedBigInteger('source_id')->nullable();
                $table->unique(['source_type', 'source_id']);
            });
        }
    }
};
