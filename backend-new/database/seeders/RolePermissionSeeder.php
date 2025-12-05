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
            ['name' => 'karyawan', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'security', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'admin', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manager', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
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
            ['name' => 'view_reports', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manage_users', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manage_roles', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
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

        // Assign specific permissions to other roles
        // Karyawan permissions
        $karyawanRoleId = DB::table('roles')->where('name', 'karyawan')->value('id');
        if ($karyawanRoleId) {
            $karyawanPermissions = DB::table('permissions')
                ->whereIn('name', ['access_admin_panel', 'view_reports'])
                ->pluck('id');

            foreach ($karyawanPermissions as $permissionId) {
                DB::table('role_has_permissions')->updateOrInsert(
                    ['role_id' => $karyawanRoleId, 'permission_id' => $permissionId],
                    ['role_id' => $karyawanRoleId, 'permission_id' => $permissionId]
                );
            }
        }

        // Security permissions
        $securityRoleId = DB::table('roles')->where('name', 'security')->value('id');
        if ($securityRoleId) {
            $securityPermissions = DB::table('permissions')
                ->whereIn('name', ['access_admin_panel', 'view_reports', 'manage_cctvs'])
                ->pluck('id');

            foreach ($securityPermissions as $permissionId) {
                DB::table('role_has_permissions')->updateOrInsert(
                    ['role_id' => $securityRoleId, 'permission_id' => $permissionId],
                    ['role_id' => $securityRoleId, 'permission_id' => $permissionId]
                );
            }
        }

        // Admin permissions
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        if ($adminRoleId) {
            $adminPermissions = DB::table('permissions')
                ->whereIn('name', ['access_admin_panel', 'view_reports', 'manage_buildings', 'manage_rooms', 'manage_contacts'])
                ->pluck('id');

            foreach ($adminPermissions as $permissionId) {
                DB::table('role_has_permissions')->updateOrInsert(
                    ['role_id' => $adminRoleId, 'permission_id' => $permissionId],
                    ['role_id' => $adminRoleId, 'permission_id' => $permissionId]
                );
            }
        }

        // Manager permissions
        $managerRoleId = DB::table('roles')->where('name', 'manager')->value('id');
        if ($managerRoleId) {
            $managerPermissions = DB::table('permissions')
                ->whereIn('name', ['access_admin_panel', 'view_reports', 'manage_users'])
                ->pluck('id');

            foreach ($managerPermissions as $permissionId) {
                DB::table('role_has_permissions')->updateOrInsert(
                    ['role_id' => $managerRoleId, 'permission_id' => $permissionId],
                    ['role_id' => $managerRoleId, 'permission_id' => $permissionId]
                );
            }
        }
    }
}