<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sgods', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., "SGOD Organizational Chart 2024"
            $table->text('description')->nullable();
            $table->string('image_path'); // Required since this is specifically for images
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sgods');
    }
};
