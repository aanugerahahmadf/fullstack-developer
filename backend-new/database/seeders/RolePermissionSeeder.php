<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            ['name' => 'super_admin', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                $role
            );
        }

        // Create permissions
        $permissions = [
            ['name' => 'access_admin_panel', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manage_buildings', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manage_rooms', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manage_cctvs', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manage_contacts', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Assign all permissions to super admin role
        $superAdminRoleId = DB::table('roles')->where('name', 'super_admin')->value('id');

        if ($superAdminRoleId) {
            $permissionIds = DB::table('permissions')->pluck('id');

            foreach ($permissionIds as $permissionId) {
                DB::table('role_has_permissions')->updateOrInsert(
                    ['role_id' => $superAdminRoleId, 'permission_id' => $permissionId],
                    ['role_id' => $superAdminRoleId, 'permission_id' => $permissionId]
                );
            }
        }
    }
}
