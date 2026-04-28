<?php

namespace App\Filament\Resources\PurchaseDetailReports\Pages;

use App\Filament\Resources\PurchaseDetailReports\PurchaseDetailReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseDetailReport extends ViewRecord
{
    protected static string $resource = PurchaseDetailReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
