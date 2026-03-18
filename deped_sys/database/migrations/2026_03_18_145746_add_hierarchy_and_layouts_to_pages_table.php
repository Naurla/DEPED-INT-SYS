<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // parent_id allows a page to belong to another page
            $table->foreignId('parent_id')->nullable()->constrained('pages')->onDelete('cascade')->after('id');
            // layout_template tells the frontend which blade file to use
            $table->string('layout_template')->default('default')->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'layout_template']);
        });
    }
};