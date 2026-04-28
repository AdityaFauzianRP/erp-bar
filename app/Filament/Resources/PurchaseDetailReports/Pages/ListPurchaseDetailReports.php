<?php

namespace App\Filament\Resources\PurchaseDetailReports\Pages;

use App\Filament\Resources\PurchaseDetailReports\PurchaseDetailReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseDetailReports extends ListRecords
{
    protected static string $resource = PurchaseDetailReportResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         CreateAction::make(),
    //     ];
    // }
}
