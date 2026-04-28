<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\OperationalReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperationalReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OperationalReport');
    }

    public function view(AuthUser $authUser, OperationalReport $operationalReport): bool
    {
        return $authUser->can('View:OperationalReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OperationalReport');
    }

    public function update(AuthUser $authUser, OperationalReport $operationalReport): bool
    {
        return $authUser->can('Update:OperationalReport');
    }

    public function delete(AuthUser $authUser, OperationalReport $operationalReport): bool
    {
        return $authUser->can('Delete:OperationalReport');
    }

}