<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add column drifted from the legacy MySQL `teachers` schema.
        // Required by `MysqlToSqliteSeeder`; without it the ETL crashes on
        // every row that carries `teacher_image` in the source payload.
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('teacher_image')->nullable()->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('teacher_image');
        });
    }
};
