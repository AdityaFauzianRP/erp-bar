<?php

namespace App\Filament\Resources\CustomerInduks\Pages;

use App\Filament\Resources\CustomerInduks\CustomerIndukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomerInduks extends ListRecords
{
    protected static string $resource = CustomerIndukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Customer Induk Baru')
                ->icon('heroicon-m-plus')
                ->color('primary'),
        ];
    }
}
