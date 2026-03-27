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
    Schema::table('site_settings', function (Blueprint $table) {
        $table->boolean('is_maintenance_mode')->default(false);
    });
}

public function down()
{
    Schema::table('site_settings', function (Blueprint $table) {
        $table->dropColumn('is_maintenance_mode');
    });
}
};
