<?php

namespace App\Filament\Resources\ReceivingReports\Pages;

use App\Filament\Resources\ReceivingReports\ReceivingReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReceivingReports extends ListRecords
{
    protected static string $resource = ReceivingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Proses Penerimaan Baru'), // <--- Tulis label di sini
        ];
    }
}
