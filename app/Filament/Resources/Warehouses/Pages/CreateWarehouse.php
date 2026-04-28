<?php

namespace App\Filament\Resources\Warehouses\Pages;

use App\Filament\Resources\Warehouses\WarehouseResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateWarehouse extends CreateRecord
{
    protected static string $resource = WarehouseResource::class;

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
