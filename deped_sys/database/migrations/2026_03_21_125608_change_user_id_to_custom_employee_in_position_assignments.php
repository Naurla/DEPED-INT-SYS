<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('position_assignments', function (Blueprint $table) {
            // Drop the foreign key and user_id column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            
            // Add custom typable fields
            $table->string('employee_name')->nullable();
            $table->string('employee_image')->nullable();
        });
    }

    public function down()
    {
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->dropColumn('employee_name');
            $table->dropColumn('employee_image');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};