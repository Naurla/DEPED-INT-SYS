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
    Schema::create('bid_opportunities', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // The title of the bid opportunity
        $table->text('description'); // The description for the public list
        $table->string('jpeg_path')->nullable(); // Path to the secure JPEG file
        $table->string('pdf_path')->nullable(); // Path to the secure PDF file
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bid_opportunities');
    }
};
