<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elementary_contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('csv_path')->nullable(); // Stores the path to the uploaded document
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elementary_contents');
    }
};