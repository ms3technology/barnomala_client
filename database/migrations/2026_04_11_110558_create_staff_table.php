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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('staff_code')->unique();
            $table->string('name');
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('photo')->nullable();
            $table->string('national_id')->nullable();
            $table->string('religion')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('marital_status')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('leaving_date')->nullable();
            // SQLite has no native ENUM type. Store as VARCHAR with a CHECK
            // constraint so invalid values are still rejected at the DB layer.
            $table->string('status', 16)->default('active');
            $table->timestamps();
        });

        // SQLite CHECK constraints can only be added in the same CREATE TABLE
        // statement on SQLite < 3.32, so we attach the guard via a trigger that
        // mirrors the original MySQL ENUM semantics.
        DB::statement(
            "CREATE TRIGGER IF NOT EXISTS trg_staff_status_check
             BEFORE INSERT ON staff
             FOR EACH ROW
             WHEN NEW.status NOT IN ('active', 'inactive', 'resigned')
             BEGIN
                 SELECT RAISE(ABORT, 'Invalid staff status value');
             END"
        );
        DB::statement(
            "CREATE TRIGGER IF NOT EXISTS trg_staff_status_check_update
             BEFORE UPDATE ON staff
             FOR EACH ROW
             WHEN NEW.status NOT IN ('active', 'inactive', 'resigned')
             BEGIN
                 SELECT RAISE(ABORT, 'Invalid staff status value');
             END"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_staff_status_check');
        DB::statement('DROP TRIGGER IF EXISTS trg_staff_status_check_update');
        Schema::dropIfExists('staff');
    }
};
