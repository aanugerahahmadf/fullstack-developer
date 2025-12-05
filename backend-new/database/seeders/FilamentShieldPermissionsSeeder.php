<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FilamentShieldPermissionsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the permissions for each resource
        $permissions = [
            // Building Permissions
            'Create:Building', 'Delete:Building', 'ForceDelete:Building', 'ForceDeleteAny:Building', 
            'Reorder:Building', 'Replicate:Building', 'Restore:Building', 'RestoreAny:Building', 
            'Update:Building', 'View:Building', 'ViewAny:Building',
            
            // Room Permissions
            'Create:Room', 'Delete:Room', 'ForceDelete:Room', 'ForceDeleteAny:Room', 
            'Reorder:Room', 'Replicate:Room', 'Restore:Room', 'RestoreAny:Room', 
            'Update:Room', 'View:Room', 'ViewAny:Room',
            
            // Contact Permissions
            'Create:Contact', 'Delete:Contact', 'ForceDelete:Contact', 'ForceDeleteAny:Contact', 
            'Reorder:Contact', 'Replicate:Contact', 'Restore:Contact', 'RestoreAny:Contact', 
            'Update:Contact', 'View:Contact', 'ViewAny:Contact',
            
            // CCTV Permissions
            'Create:Cctv', 'Delete:Cctv', 'ForceDelete:Cctv', 'ForceDeleteAny:Cctv', 
            'Reorder:Cctv', 'Replicate:Cctv', 'Restore:Cctv', 'RestoreAny:Cctv', 
            'Update:Cctv', 'View:Cctv', 'ViewAny:Cctv',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $allPermissions = Permission::all();
            $superAdminRole->syncPermissions($allPermissions);
        }
    }
}