<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\OperationalItemReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperationalItemReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OperationalItemReport');
    }

    public function view(AuthUser $authUser, OperationalItemReport $operationalItemReport): bool
    {
        return $authUser->can('View:OperationalItemReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OperationalItemReport');
    }

    public function update(AuthUser $authUser, OperationalItemReport $operationalItemReport): bool
    {
        return $authUser->can('Update:OperationalItemReport');
    }

    public function delete(AuthUser $authUser, OperationalItemReport $operationalItemReport): bool
    {
        return $authUser->can('Delete:OperationalItemReport');
    }

}