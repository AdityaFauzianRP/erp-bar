<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Delivery;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeliveryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Delivery');
    }

    public function view(AuthUser $authUser, Delivery $delivery): bool
    {
        return $authUser->can('View:Delivery');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Delivery');
    }

    public function update(AuthUser $authUser, Delivery $delivery): bool
    {
        return $authUser->can('Update:Delivery');
    }

    public function delete(AuthUser $authUser, Delivery $delivery): bool
    {
        return $authUser->can('Delete:Delivery');
    }

}