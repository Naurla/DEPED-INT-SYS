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
    Schema::table('bid_opportunities', function (Blueprint $table) {
        $table->string('excel_path')->nullable()->after('pdf_path');
    });
}

public function down()
{
    Schema::table('bid_opportunities', function (Blueprint $table) {
        $table->dropColumn('excel_path');
    });
}
};
