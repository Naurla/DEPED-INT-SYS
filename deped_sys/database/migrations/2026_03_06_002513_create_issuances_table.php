<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('issuances', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable(); // Add this line
        $table->string('type'); // Will store 'advisory', 'memorandum', or 'hrmpsb'
        $table->string('pdf_path');
        $table->string('image_path')->nullable(); // For the red image/thumbnail
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('issuances');
}
};
