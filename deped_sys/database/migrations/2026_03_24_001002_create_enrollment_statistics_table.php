<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enrollment_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('school_year')->nullable();
            $table->text('content')->nullable();
            $table->string('image_path')->nullable();
            $table->string('file_path')->nullable(); // For PDF or Excel attachments
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrollment_statistics');
    }
};