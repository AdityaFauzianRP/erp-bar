<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\OperationalProduct;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperationalProductPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OperationalProduct');
    }

    public function view(AuthUser $authUser, OperationalProduct $operationalProduct): bool
    {
        return $authUser->can('View:OperationalProduct');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OperationalProduct');
    }

    public function update(AuthUser $authUser, OperationalProduct $operationalProduct): bool
    {
        return $authUser->can('Update:OperationalProduct');
    }

    public function delete(AuthUser $authUser, OperationalProduct $operationalProduct): bool
    {
        return $authUser->can('Delete:OperationalProduct');
    }

}