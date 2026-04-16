<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add to Elementary
        Schema::table('elementary_contents', function (Blueprint $table) {
            $table->enum('school_type', ['public', 'private'])->default('public')->after('title');
        });

        // Add to Junior High
        Schema::table('junior_high_contents', function (Blueprint $table) {
            $table->enum('school_type', ['public', 'private'])->default('public')->after('title');
        });

        // Add to Senior High
        Schema::table('senior_high_contents', function (Blueprint $table) {
            $table->enum('school_type', ['public', 'private'])->default('public')->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('elementary_contents', function (Blueprint $table) { $table->dropColumn('school_type'); });
        Schema::table('junior_high_contents', function (Blueprint $table) { $table->dropColumn('school_type'); });
        Schema::table('senior_high_contents', function (Blueprint $table) { $table->dropColumn('school_type'); });
    }
};