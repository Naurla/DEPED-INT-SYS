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
    Schema::table('data_privacies', function (Blueprint $table) {
        $table->json('sections')->nullable()->after('notice');
    });
}

public function down(): void
{
    Schema::table('data_privacies', function (Blueprint $table) {
        $table->dropColumn('sections');
    });
}
};
