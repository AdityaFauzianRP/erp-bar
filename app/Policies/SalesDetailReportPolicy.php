<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SalesDetailReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesDetailReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SalesDetailReport');
    }

    public function view(AuthUser $authUser, SalesDetailReport $salesDetailReport): bool
    {
        return $authUser->can('View:SalesDetailReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SalesDetailReport');
    }

    public function update(AuthUser $authUser, SalesDetailReport $salesDetailReport): bool
    {
        return $authUser->can('Update:SalesDetailReport');
    }

    public function delete(AuthUser $authUser, SalesDetailReport $salesDetailReport): bool
    {
        return $authUser->can('Delete:SalesDetailReport');
    }

}