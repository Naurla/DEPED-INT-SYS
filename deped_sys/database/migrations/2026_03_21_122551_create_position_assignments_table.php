<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('position_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); 
            $table->integer('slot_index'); // Slot 1, Slot 2, etc.
            
            // A specific slot in a specific position can only be occupied once
            $table->unique(['position_id', 'slot_index']); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('position_assignments');
    }
};