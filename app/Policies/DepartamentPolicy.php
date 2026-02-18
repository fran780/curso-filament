<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Departament;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartamentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Departament');
    }

    public function view(AuthUser $authUser, Departament $departament): bool
    {
        return $authUser->can('View:Departament');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Departament');
    }

    public function update(AuthUser $authUser, Departament $departament): bool
    {
        return $authUser->can('Update:Departament');
    }

    public function delete(AuthUser $authUser, Departament $departament): bool
    {
        return $authUser->can('Delete:Departament');
    }

    public function restore(AuthUser $authUser, Departament $departament): bool
    {
        return $authUser->can('Restore:Departament');
    }

    public function forceDelete(AuthUser $authUser, Departament $departament): bool
    {
        return $authUser->can('ForceDelete:Departament');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Departament');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Departament');
    }

    public function replicate(AuthUser $authUser, Departament $departament): bool
    {
        return $authUser->can('Replicate:Departament');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Departament');
    }

}