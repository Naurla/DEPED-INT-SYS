<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('als_implementers', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., "Juan Dela Cruz - January 2024"
            $table->longText('content')->nullable();
            $table->string('image_path')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('als_implementers');
    }
};