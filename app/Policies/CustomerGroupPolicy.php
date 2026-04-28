<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CustomerGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerGroupPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CustomerGroup');
    }

    public function view(AuthUser $authUser, CustomerGroup $customerGroup): bool
    {
        return $authUser->can('View:CustomerGroup');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CustomerGroup');
    }

    public function update(AuthUser $authUser, CustomerGroup $customerGroup): bool
    {
        return $authUser->can('Update:CustomerGroup');
    }

    public function delete(AuthUser $authUser, CustomerGroup $customerGroup): bool
    {
        return $authUser->can('Delete:CustomerGroup');
    }

}