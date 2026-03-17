<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qms', function (Blueprint $table) {
            $table->id();
            $table->longText('scope')->nullable();
            $table->longText('policy')->nullable();
            $table->longText('objective')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qms');
    }
};