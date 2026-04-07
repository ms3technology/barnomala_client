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
        Schema::create('speeches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title'); // E.g., Chairman's Message
            $table->string('designation')->nullable();
            $table->text('speech')->nullable();
            $table->json('image_json')->nullable();
            $table->integer('row_index')->default(1);
            $table->integer('column_index')->default(1); // 1, 2, 3 in a 3-column grid
            $table->integer('colspan')->default(1); // 1, 2, or 3
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speeches');
    }
};
