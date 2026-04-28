<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PurchaseReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PurchaseReport');
    }

    public function view(AuthUser $authUser, PurchaseReport $purchaseReport): bool
    {
        return $authUser->can('View:PurchaseReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PurchaseReport');
    }

    public function update(AuthUser $authUser, PurchaseReport $purchaseReport): bool
    {
        return $authUser->can('Update:PurchaseReport');
    }

    public function delete(AuthUser $authUser, PurchaseReport $purchaseReport): bool
    {
        return $authUser->can('Delete:PurchaseReport');
    }

}