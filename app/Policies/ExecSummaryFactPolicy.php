<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ExecSummaryFact;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExecSummaryFactPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExecSummaryFact');
    }

    public function view(AuthUser $authUser, ExecSummaryFact $execSummaryFact): bool
    {
        return $authUser->can('View:ExecSummaryFact');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExecSummaryFact');
    }

    public function update(AuthUser $authUser, ExecSummaryFact $execSummaryFact): bool
    {
        return $authUser->can('Update:ExecSummaryFact');
    }

    public function delete(AuthUser $authUser, ExecSummaryFact $execSummaryFact): bool
    {
        return $authUser->can('Delete:ExecSummaryFact');
    }

}