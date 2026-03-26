<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // Drop the old single columns if they exist
            if (Schema::hasColumn('pages', 'featured_video_url')) {
                $table->dropColumn(['featured_video_url', 'video_shape']);
            }
            // Add the new JSON array column
            $table->json('featured_videos')->nullable()->after('layout_template');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('featured_videos');
        });
    }
};