<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SalesReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SalesReport');
    }

    public function view(AuthUser $authUser, SalesReport $salesReport): bool
    {
        return $authUser->can('View:SalesReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SalesReport');
    }

    public function update(AuthUser $authUser, SalesReport $salesReport): bool
    {
        return $authUser->can('Update:SalesReport');
    }

    public function delete(AuthUser $authUser, SalesReport $salesReport): bool
    {
        return $authUser->can('Delete:SalesReport');
    }

}