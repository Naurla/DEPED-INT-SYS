<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('learning_strands', function (Blueprint $table) {
            $table->string('content_title')->nullable()->after('name');
            $table->text('content_description')->nullable()->after('content_title');
        });
    }

    public function down()
    {
        Schema::table('learning_strands', function (Blueprint $table) {
            $table->dropColumn(['content_title', 'content_description']);
        });
    }
};