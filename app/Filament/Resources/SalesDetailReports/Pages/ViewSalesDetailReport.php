<?php

namespace App\Filament\Resources\SalesDetailReports\Pages;

use App\Filament\Resources\SalesDetailReports\SalesDetailReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSalesDetailReport extends ViewRecord
{
    protected static string $resource = SalesDetailReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
