<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_sections', function (Blueprint $table) {
            // Store the raw video URL (YouTube, Facebook, TikTok)
            $table->string('video_url')->nullable()->after('image_path');
            // 'landscape' or 'portrait'
            $table->string('video_shape')->default('landscape')->after('video_url');
            // Optional caption shown below the video
            $table->string('video_caption')->nullable()->after('video_shape');
        });
    }

    public function down(): void
    {
        Schema::table('page_sections', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'video_shape', 'video_caption']);
        });
    }
};
