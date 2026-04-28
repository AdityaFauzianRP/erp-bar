<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    // 1. Arahkan ke file Blade kustom nanti
    protected string $view = 'filament.resources.transactions.custom-page';

    // 2. Set Lebar Maksimal
    public function getMaxContentWidth(): string
    {
        return 'full';
    }
}
