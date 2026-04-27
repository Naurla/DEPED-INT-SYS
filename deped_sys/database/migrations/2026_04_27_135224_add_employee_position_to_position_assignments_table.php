<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('position_assignments', function (Blueprint $table) {
            // Add the new column after employee_name
            $table->string('employee_position')->nullable()->after('employee_name');
        });
    }

    public function down()
    {
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->dropColumn('employee_position');
        });
    }
};