<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_logos', function (Blueprint $blueprint) {
            // Change the 'name' column from nullable to required
            $blueprint->string('name')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('site_logos', function (Blueprint $blueprint) {
            // Revert back to nullable if necessary
            $blueprint->string('name')->nullable()->change();
        });
    }
};