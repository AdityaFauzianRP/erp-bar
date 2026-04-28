<?php

namespace App\Filament\Resources\PurchaseReports\Pages;

use App\Filament\Resources\PurchaseReports\PurchaseReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseReports extends ListRecords
{
    protected static string $resource = PurchaseReportResource::class;

}
