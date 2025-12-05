<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /**
     * Get the role description based on the role name.
     *
     * @return string
     */
    public function getDescriptionAttribute(): string
    {
        return match($this->name) {
            'super_admin' => 'Has all permissions',
            'security' => 'Can access admin panel, view reports, and manage CCTV',
            'admin' => 'Can access admin panel, view reports, and manage buildings, rooms, and contacts',
            'manager' => 'Can access admin panel, view reports, and manage users',
            'karyawan' => 'Can access admin panel and view reports',
            default => 'No description available',
        };
    }

    /**
     * Get permissions list for the role.
     *
     * @return array
     */
    public function getPermissionsListAttribute(): array
    {
        return match($this->name) {
            'super_admin' => [
                'All permissions',
            ],
            'security' => [
                'Access admin panel',
                'View reports',
                'Manage CCTV',
            ],
            'admin' => [
                'Access admin panel',
                'View reports',
                'Manage buildings',
                'Manage rooms',
                'Manage contacts',
            ],
            'manager' => [
                'Access admin panel',
                'View reports',
                'Manage users',
            ],
            'karyawan' => [
                'Access admin panel',
                'View reports',
            ],
            default => [],
        };
    }
}