<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('display_location'); // e.g., 'home', 'procurement', 'page:about-us'
            $table->string('type'); // e.g., 'banner', 'rich_text'
            $table->string('title')->nullable(); // Optional heading for the text block
            $table->longText('content')->nullable(); // Stores the text/HTML
            $table->string('image_path')->nullable(); // Stores image if it's a banner
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};