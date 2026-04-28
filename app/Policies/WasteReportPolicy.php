<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\WasteReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class WasteReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WasteReport');
    }

    public function view(AuthUser $authUser, WasteReport $wasteReport): bool
    {
        return $authUser->can('View:WasteReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WasteReport');
    }

    public function update(AuthUser $authUser, WasteReport $wasteReport): bool
    {
        return $authUser->can('Update:WasteReport');
    }

    public function delete(AuthUser $authUser, WasteReport $wasteReport): bool
    {
        return $authUser->can('Delete:WasteReport');
    }

}