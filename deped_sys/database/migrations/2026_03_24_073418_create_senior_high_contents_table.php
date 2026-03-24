<?php
// Run this in your terminal: php artisan make:migration create_senior_high_contents_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('senior_high_contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('csv_path')->nullable(); // Stores path to SHS CSV files
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('senior_high_contents');
    }
};