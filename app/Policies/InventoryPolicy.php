<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Inventory;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Inventory');
    }

    public function view(AuthUser $authUser, Inventory $inventory): bool
    {
        return $authUser->can('View:Inventory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Inventory');
    }

    public function update(AuthUser $authUser, Inventory $inventory): bool
    {
        return $authUser->can('Update:Inventory');
    }

    public function delete(AuthUser $authUser, Inventory $inventory): bool
    {
        return $authUser->can('Delete:Inventory');
    }

}