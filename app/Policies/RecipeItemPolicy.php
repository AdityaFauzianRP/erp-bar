<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RecipeItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecipeItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RecipeItem');
    }

    public function view(AuthUser $authUser, RecipeItem $recipeItem): bool
    {
        return $authUser->can('View:RecipeItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RecipeItem');
    }

    public function update(AuthUser $authUser, RecipeItem $recipeItem): bool
    {
        return $authUser->can('Update:RecipeItem');
    }

    public function delete(AuthUser $authUser, RecipeItem $recipeItem): bool
    {
        return $authUser->can('Delete:RecipeItem');
    }

}