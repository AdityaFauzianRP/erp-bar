<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\OperationalExpense;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperationalExpensePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OperationalExpense');
    }

    public function view(AuthUser $authUser, OperationalExpense $operationalExpense): bool
    {
        return $authUser->can('View:OperationalExpense');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OperationalExpense');
    }

    public function update(AuthUser $authUser, OperationalExpense $operationalExpense): bool
    {
        return $authUser->can('Update:OperationalExpense');
    }

    public function delete(AuthUser $authUser, OperationalExpense $operationalExpense): bool
    {
        return $authUser->can('Delete:OperationalExpense');
    }

}