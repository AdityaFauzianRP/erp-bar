<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Resources\Branches\BranchResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateBranch extends CreateRecord
{
    protected static string $resource = BranchResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan Data ');
    }
    

    // 2. Menghilangkan Tombol "Create & Create Another"
    protected function getCreateAnotherFormAction(): Action
    {
        // Kita buat action kosong (empty) agar tidak muncul di view
        return parent::getCreateAnotherFormAction()
            ->hidden();
    }
}
