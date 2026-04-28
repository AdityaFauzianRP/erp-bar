<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProductUsageReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductUsageReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProductUsageReport');
    }

    public function view(AuthUser $authUser, ProductUsageReport $productUsageReport): bool
    {
        return $authUser->can('View:ProductUsageReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProductUsageReport');
    }

    public function update(AuthUser $authUser, ProductUsageReport $productUsageReport): bool
    {
        return $authUser->can('Update:ProductUsageReport');
    }

    public function delete(AuthUser $authUser, ProductUsageReport $productUsageReport): bool
    {
        return $authUser->can('Delete:ProductUsageReport');
    }

}