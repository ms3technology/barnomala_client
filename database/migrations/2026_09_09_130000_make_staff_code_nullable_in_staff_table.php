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
        // Drop the existing unique index so we can rebuild it on the new
        // nullable column. In MySQL a unique index allows multiple NULL
        // values, so the uniqueness semantics for non-null codes are
        // preserved.
        Schema::table('staff', function (Blueprint $table) {
            $table->dropUnique(['staff_code']);
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->string('staff_code')->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropUnique(['staff_code']);
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->string('staff_code')->nullable(false)->unique()->change();
        });
    }
};
