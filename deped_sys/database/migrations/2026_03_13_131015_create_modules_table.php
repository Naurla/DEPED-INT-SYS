<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('k12_modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('file_path');
            $table->string('file_type');
            $table->string('image_path')->nullable(); // Added picture upload field
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('k12_modules');
    }
};