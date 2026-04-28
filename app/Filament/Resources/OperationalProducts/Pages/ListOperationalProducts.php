<?php

namespace App\Filament\Resources\OperationalProducts\Pages;

use App\Filament\Resources\OperationalProducts\OperationalProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOperationalProducts extends ListRecords
{
    protected static string $resource = OperationalProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Produk Operational')
                ->icon('heroicon-m-plus')
                ->color('primary'),
        ];
    }
}
