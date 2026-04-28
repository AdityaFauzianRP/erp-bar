<?php

namespace App\Filament\Resources\OperationalItemReports\Pages;

use App\Filament\Resources\OperationalItemReports\OperationalItemReportResource;
use Filament\Resources\Pages\ListRecords;

class ListOperationalItemReports extends ListRecords
{
    protected static string $resource = OperationalItemReportResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         CreateAction::make(),
    //     ];
    // }
}
