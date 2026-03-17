<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citizen_charters', function (Blueprint $table) {
            $table->id();
            $table->longText('content')->nullable();
            $table->string('file_path')->nullable(); // Stores the PDF
            $table->string('file_name')->nullable(); // Stores the name of the PDF
            $table->json('links')->nullable();       // Stores an array of {name, url}
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citizen_charters');
    }
};