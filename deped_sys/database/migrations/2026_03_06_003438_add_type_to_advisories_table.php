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
    Schema::table('advisories', function (Blueprint $table) {
        // We'll add 'type' and default it to 'advisory' so your existing records don't break
        $table->string('type')->default('advisory')->after('title');
    });
}

public function down(): void
{
    Schema::table('advisories', function (Blueprint $table) {
        $table->dropColumn('type');
    });
}
};
