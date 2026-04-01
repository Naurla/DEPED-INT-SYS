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
    Schema::table('issuances', function (Blueprint $table) { // Use 'bid_opportunities' for the other file
        $table->date('date')->nullable()->after('title');
    });
}

public function down()
{
    Schema::table('issuances', function (Blueprint $table) { // Use 'bid_opportunities' for the other file
        $table->dropColumn('date');
    });
}
};
