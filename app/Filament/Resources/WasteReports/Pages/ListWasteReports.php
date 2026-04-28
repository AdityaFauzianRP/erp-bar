<?php

namespace App\Filament\Resources\WasteReports\Pages;

use App\Filament\Resources\WasteReports\WasteReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWasteReports extends ListRecords
{
    protected static string $resource = WasteReportResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         CreateAction::make(),
    //     ];
    // }
}
