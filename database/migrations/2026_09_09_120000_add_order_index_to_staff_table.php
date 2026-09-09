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
        Schema::table('staff', function (Blueprint $table) {
            // Manual sort order for staff and incharges pages. Nullable so
            // existing rows keep their previous (staff_code / name) ordering
            // until an admin sets an explicit position. Not unique because
            // multiple rows may share the same priority within a list.
            $table->integer('order_index')->nullable()->after('staff_code')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropIndex(['order_index']);
            $table->dropColumn('order_index');
        });
    }
};
