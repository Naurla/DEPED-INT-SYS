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
        $table->json('disabled_pages')->nullable(); // Stores our array of disabled routes
    });
}

public function down()
{
    Schema::table('site_settings', function (Blueprint $table) {
        $table->dropColumn('disabled_pages');
    });
}
};
