<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the super admin role if it doesn't exist
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin', // Changed to match the existing role name
            'guard_name' => 'web'
        ], [
            'name' => 'Super Admin', // Changed to match the existing role name
            'guard_name' => 'web'
        ]);

        // Create or update the super admin user
        $userData = [
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'admin@pertamina.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ];

        $user = User::updateOrCreate(
            ['email' => 'admin@pertamina.com'],
            $userData
        );

        // Assign the super admin role to the user
        $user->assignRole('Super Admin'); // Changed to match the existing role name
    }
}