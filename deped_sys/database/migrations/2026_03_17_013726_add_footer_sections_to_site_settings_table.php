<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('site_settings', function (Blueprint $table) {
        $table->json('footer_sections')->nullable();
    });
}

public function down(): void
{
    Schema::table('site_settings', function (Blueprint $table) {
        $table->dropColumn('footer_sections');
    });
}
};
