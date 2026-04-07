<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            // Adds a JSON column to store the checklist array
            $table->json('permissions')->nullable()->after('slug');
        });
    }

    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};