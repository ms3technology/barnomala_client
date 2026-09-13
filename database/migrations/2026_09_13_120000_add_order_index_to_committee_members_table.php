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
        Schema::table('committee_members', function (Blueprint $table) {
            // Manual sort order for committee member listings. Nullable so
            // existing rows keep their previous (id-based) ordering until an
            // admin sets an explicit position. Not unique because multiple
            // members may share the same priority within a committee.
            $table->integer('order_index')->nullable()->after('designation')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('committee_members', function (Blueprint $table) {
            $table->dropIndex(['order_index']);
            $table->dropColumn('order_index');
        });
    }
};
