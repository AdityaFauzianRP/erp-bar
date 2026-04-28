<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PurchaseDetailReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseDetailReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PurchaseDetailReport');
    }

    public function view(AuthUser $authUser, PurchaseDetailReport $purchaseDetailReport): bool
    {
        return $authUser->can('View:PurchaseDetailReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PurchaseDetailReport');
    }

    public function update(AuthUser $authUser, PurchaseDetailReport $purchaseDetailReport): bool
    {
        return $authUser->can('Update:PurchaseDetailReport');
    }

    public function delete(AuthUser $authUser, PurchaseDetailReport $purchaseDetailReport): bool
    {
        return $authUser->can('Delete:PurchaseDetailReport');
    }

}