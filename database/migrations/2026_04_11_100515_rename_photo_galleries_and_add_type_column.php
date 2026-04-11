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
        Schema::rename('photo_galleries', 'galleries');
        Schema::table('galleries', function (Blueprint $table) {
            $table->string('type')->default('photo')->after('id'); // photo, video
            $table->string('video_url')->nullable()->after('image_path');
            $table->string('video_path')->nullable();
            $table->string('image_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->string('image_path')->nullable(false)->change();
            $table->dropColumn(['type', 'video_url', 'video_path']);
        });
        Schema::rename('galleries', 'photo_galleries');
    }
};
