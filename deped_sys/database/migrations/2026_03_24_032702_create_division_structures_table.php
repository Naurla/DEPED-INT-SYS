<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('division_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., OSDS, SGOD, CID
            $table->string('type')->default('division'); // Division, Section, Unit
            $table->integer('order_no')->default(1);
            
            // We use JSON columns to store multiple dynamic inputs
            $table->json('descriptions')->nullable(); // Array of text blocks
            $table->string('main_photo')->nullable(); // Path to main image
            $table->json('gallery_images')->nullable(); // Array of paths for gallery
            $table->json('pdf_documents')->nullable(); // Array of PDF data (name, path, size)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('division_structures');
    }
};