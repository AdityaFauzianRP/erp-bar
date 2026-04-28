<?php

namespace App\Filament\Resources\OperationalProducts\Pages;

use App\Filament\Resources\OperationalProducts\OperationalProductResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateOperationalProduct extends CreateRecord
{
    protected static string $resource = OperationalProductResource::class;

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
