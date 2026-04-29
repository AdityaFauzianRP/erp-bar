<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RawMaterial;
use Illuminate\Auth\Access\HandlesAuthorization;

class RawMaterialPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RawMaterial');
    }

    public function view(AuthUser $authUser, RawMaterial $rawMaterial): bool
    {
        return $authUser->can('View:RawMaterial');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RawMaterial');
    }

    public function update(AuthUser $authUser, RawMaterial $rawMaterial): bool
    {
        return $authUser->can('Update:RawMaterial');
    }

    public function delete(AuthUser $authUser, RawMaterial $rawMaterial): bool
    {
        return $authUser->can('Delete:RawMaterial');
    }

}