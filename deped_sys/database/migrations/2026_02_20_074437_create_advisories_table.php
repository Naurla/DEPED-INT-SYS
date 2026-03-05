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
    Schema::create('advisories', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('image_path'); // Stores: advisories/images/filename.jpg
        $table->string('pdf_path');   // Stores: advisories/pdfs/filename.pdf
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advisories');
    }
};
