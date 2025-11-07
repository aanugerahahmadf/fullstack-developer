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
            ['name' => 'super_admin', 'display_name' => 'Super Admin', 'description' => 'Full access to all system features'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                $role
            );
        }

        // Create permissions
        $permissions = [
            ['name' => 'access_admin_panel', 'display_name' => 'Access Admin Panel', 'description' => 'Allow access to the admin panel'],
            ['name' => 'manage_buildings', 'display_name' => 'Manage Buildings', 'description' => 'Create, update, delete buildings'],
            ['name' => 'manage_rooms', 'display_name' => 'Manage Rooms', 'description' => 'Create, update, delete rooms'],
            ['name' => 'manage_cctvs', 'display_name' => 'Manage CCTVs', 'description' => 'Create, update, delete CCTVs'],
            ['name' => 'manage_contacts', 'display_name' => 'Manage Contacts', 'description' => 'Create, update, delete contacts'],
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
                DB::table('role_permissions')->updateOrInsert(
                    ['role_id' => $superAdminRoleId, 'permission_id' => $permissionId],
                    ['role_id' => $superAdminRoleId, 'permission_id' => $permissionId]
                );
            }
        }
    }
}
