<?php

namespace App\Filament\Resources\SalesDetailReports\Pages;

use App\Filament\Resources\SalesDetailReports\SalesDetailReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSalesDetailReports extends ListRecords
{
    protected static string $resource = SalesDetailReportResource::class;

    // tidak bisa edit 

}
