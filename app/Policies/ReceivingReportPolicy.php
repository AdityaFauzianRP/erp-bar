<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ReceivingReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReceivingReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ReceivingReport');
    }

    public function view(AuthUser $authUser, ReceivingReport $receivingReport): bool
    {
        return $authUser->can('View:ReceivingReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ReceivingReport');
    }

    public function update(AuthUser $authUser, ReceivingReport $receivingReport): bool
    {
        return $authUser->can('Update:ReceivingReport');
    }

    public function delete(AuthUser $authUser, ReceivingReport $receivingReport): bool
    {
        return $authUser->can('Delete:ReceivingReport');
    }

}