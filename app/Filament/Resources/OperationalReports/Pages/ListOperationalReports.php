<?php

namespace App\Filament\Resources\OperationalReports\Pages;

use App\Filament\Resources\OperationalReports\OperationalReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOperationalReports extends ListRecords
{
    protected static string $resource = OperationalReportResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         CreateAction::make(),
    //     ];
    // }
}
