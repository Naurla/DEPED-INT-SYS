<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'Superintendent', 'Director'
            $table->integer('slots_count')->default(1); // How many people can hold this
            $table->foreignId('parent_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('positions');
    }
};