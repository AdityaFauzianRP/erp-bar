<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ViewRencanaPemakaianProduk;
use Illuminate\Auth\Access\HandlesAuthorization;

class ViewRencanaPemakaianProdukPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ViewRencanaPemakaianProduk');
    }

    public function view(AuthUser $authUser, ViewRencanaPemakaianProduk $viewRencanaPemakaianProduk): bool
    {
        return $authUser->can('View:ViewRencanaPemakaianProduk');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ViewRencanaPemakaianProduk');
    }

    public function update(AuthUser $authUser, ViewRencanaPemakaianProduk $viewRencanaPemakaianProduk): bool
    {
        return $authUser->can('Update:ViewRencanaPemakaianProduk');
    }

    public function delete(AuthUser $authUser, ViewRencanaPemakaianProduk $viewRencanaPemakaianProduk): bool
    {
        return $authUser->can('Delete:ViewRencanaPemakaianProduk');
    }

}