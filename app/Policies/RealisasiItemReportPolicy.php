<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RealisasiItemReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class RealisasiItemReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RealisasiItemReport');
    }

    public function view(AuthUser $authUser, RealisasiItemReport $realisasiItemReport): bool
    {
        return $authUser->can('View:RealisasiItemReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RealisasiItemReport');
    }

    public function update(AuthUser $authUser, RealisasiItemReport $realisasiItemReport): bool
    {
        return $authUser->can('Update:RealisasiItemReport');
    }

    public function delete(AuthUser $authUser, RealisasiItemReport $realisasiItemReport): bool
    {
        return $authUser->can('Delete:RealisasiItemReport');
    }

}