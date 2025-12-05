<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Cctv;
use Illuminate\Auth\Access\HandlesAuthorization;

class CctvPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Cctv');
    }

    public function view(AuthUser $authUser, Cctv $cctv): bool
    {
        return $authUser->can('View:Cctv');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Cctv');
    }

    public function update(AuthUser $authUser, Cctv $cctv): bool
    {
        return $authUser->can('Update:Cctv');
    }

    public function delete(AuthUser $authUser, Cctv $cctv): bool
    {
        return $authUser->can('Delete:Cctv');
    }

    public function restore(AuthUser $authUser, Cctv $cctv): bool
    {
        return $authUser->can('Restore:Cctv');
    }

    public function forceDelete(AuthUser $authUser, Cctv $cctv): bool
    {
        return $authUser->can('ForceDelete:Cctv');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Cctv');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Cctv');
    }

    public function replicate(AuthUser $authUser, Cctv $cctv): bool
    {
        return $authUser->can('Replicate:Cctv');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Cctv');
    }

}