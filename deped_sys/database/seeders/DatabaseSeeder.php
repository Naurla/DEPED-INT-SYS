<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create the necessary roles
        $superAdminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);
        Role::firstOrCreate(['slug' => 'info-office'], ['name' => 'Information Office']);
        Role::firstOrCreate(['slug' => 'issuance-manager'], ['name' => 'Issuance Manager']);

        // 2. Find your existing admin account by its email, or create it if it doesn't exist
        // IMPORTANT: Change 'admin@example.com' to the actual email you use to log in!
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'], 
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'), // Change this if you want a different default password
                'requires_password_change' => false
            ]
        );

        // 3. Attach the Super Admin role to your account
        $admin->role_id = $superAdminRole->id;
        $admin->save();
    }
}