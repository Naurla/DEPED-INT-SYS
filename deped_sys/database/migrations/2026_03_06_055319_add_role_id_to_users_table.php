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
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
        // If you want to force password changes on first login:
        $table->boolean('requires_password_change')->default(false); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Drop the foreign key constraint first
            $table->dropForeign(['role_id']);
            
            // 2. Drop the columns
            $table->dropColumn(['role_id', 'requires_password_change']);
        });
    }
};
