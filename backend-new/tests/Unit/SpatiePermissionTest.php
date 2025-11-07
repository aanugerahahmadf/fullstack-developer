<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SpatiePermissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function check_permission_to_method_exists()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a permission
        $permission = Permission::create(['name' => 'edit articles']);

        // Create a role and assign permission
        $role = Role::create(['name' => 'writer']);
        $role->givePermissionTo($permission);

        // Assign role to user
        $user->assignRole($role);

        // Test that the checkPermissionTo method exists and works
        $this->assertTrue(method_exists($user, 'checkPermissionTo'));
        $this->assertTrue($user->checkPermissionTo('edit articles'));
        $this->assertFalse($user->checkPermissionTo('delete articles'));
    }
}
