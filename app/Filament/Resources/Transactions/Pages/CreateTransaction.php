<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord; // Sesuaikan (CreateRecord/EditRecord/ViewRecord)

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    // HAPUS KATA 'static' DI SINI
    protected string $view = 'filament.resources.transactions.custom-page';

    public function getMaxContentWidth(): string
    {
        return 'full';
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