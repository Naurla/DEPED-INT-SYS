<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // CHANGE 'issuances' to 'bid_opportunities' HERE
        Schema::table('bid_opportunities', function (Blueprint $table) { 
            $table->date('date')->nullable()->after('category'); // You can put it after category or title
        });
    }

    public function down()
    {
        // CHANGE 'issuances' to 'bid_opportunities' HERE
        Schema::table('bid_opportunities', function (Blueprint $table) { 
            $table->dropColumn('date');
        });
    }
};