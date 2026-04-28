<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CustomerInduk;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerIndukPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CustomerInduk');
    }

    public function view(AuthUser $authUser, CustomerInduk $customerInduk): bool
    {
        return $authUser->can('View:CustomerInduk');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CustomerInduk');
    }

    public function update(AuthUser $authUser, CustomerInduk $customerInduk): bool
    {
        return $authUser->can('Update:CustomerInduk');
    }

    public function delete(AuthUser $authUser, CustomerInduk $customerInduk): bool
    {
        return $authUser->can('Delete:CustomerInduk');
    }

}