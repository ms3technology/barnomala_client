<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // general, managing etc..
            $table->string('name');
            $table->string('session')->nullable(); // 2024-2025
            $table->text('description')->nullable();
            $table->integer('order_index')->default(0);
            // SQLite has no native ENUM type — store as VARCHAR with a CHECK
            // trigger that mirrors the original MySQL ENUM constraint.
            $table->string('status', 16)->default('active');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        DB::statement(
            "CREATE TRIGGER IF NOT EXISTS trg_committees_status_check
             BEFORE INSERT ON committees
             FOR EACH ROW
             WHEN NEW.status NOT IN ('active', 'inactive')
             BEGIN
                 SELECT RAISE(ABORT, 'Invalid committee status value');
             END"
        );
        DB::statement(
            "CREATE TRIGGER IF NOT EXISTS trg_committees_status_check_update
             BEFORE UPDATE ON committees
             FOR EACH ROW
             WHEN NEW.status NOT IN ('active', 'inactive')
             BEGIN
                 SELECT RAISE(ABORT, 'Invalid committee status value');
             END"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_committees_status_check');
        DB::statement('DROP TRIGGER IF EXISTS trg_committees_status_check_update');
        Schema::dropIfExists('committees');
    }
};
