<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SchoolCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SchoolCategory');
    }

    public function view(AuthUser $authUser, SchoolCategory $schoolCategory): bool
    {
        return $authUser->can('View:SchoolCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SchoolCategory');
    }

    public function update(AuthUser $authUser, SchoolCategory $schoolCategory): bool
    {
        return $authUser->can('Update:SchoolCategory');
    }

    public function delete(AuthUser $authUser, SchoolCategory $schoolCategory): bool
    {
        return $authUser->can('Delete:SchoolCategory');
    }

}