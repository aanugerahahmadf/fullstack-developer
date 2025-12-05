<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateExportPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:create-export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create export permissions for all resources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if permissions table exists
        if (!Schema::hasTable('permissions')) {
            $this->error('Permissions table does not exist. Please run migrations first.');
            return 1;
        }

        // Resources that need export permissions
        $resources = ['Building', 'Cctv', 'Contact', 'Role', 'Room', 'User'];

        foreach ($resources as $resource) {
            $permissionName = "Export:{$resource}";
            
            // Check if permission already exists
            $exists = DB::table('permissions')->where('name', $permissionName)->exists();
            
            if (!$exists) {
                // Create the permission
                DB::table('permissions')->insert([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $this->info("Created permission: {$permissionName}");
            } else {
                $this->info("Permission already exists: {$permissionName}");
            }
        }

        // Assign all export permissions to Super Admin role (role_id = 1)
        $exportPermissions = DB::table('permissions')->where('name', 'like', 'Export:%')->pluck('id');
        
        foreach ($exportPermissions as $permissionId) {
            $exists = DB::table('role_has_permissions')
                ->where('role_id', 1)
                ->where('permission_id', $permissionId)
                ->exists();
                
            if (!$exists) {
                DB::table('role_has_permissions')->insert([
                    'role_id' => 1,
                    'permission_id' => $permissionId,
                ]);
                
                $permissionName = DB::table('permissions')->where('id', $permissionId)->value('name');
                $this->info("Assigned permission {$permissionName} to Super Admin role");
            }
        }

        $this->info('Export permissions created and assigned to Super Admin role successfully!');
        return 0;
    }
}