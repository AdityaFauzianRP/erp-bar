<?php

namespace App\Filament\Resources\OperationalExpenses\Pages;

use App\Filament\Resources\OperationalExpenses\OperationalExpenseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOperationalExpenses extends ListRecords
{
    protected static string $resource = OperationalExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Pengajuan Biaya Operasional')
                ->icon('heroicon-o-plus'),
        ];
    }
}
