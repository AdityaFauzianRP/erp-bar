<?php

namespace App\Filament\Resources\PurchaseReports\Pages;

use App\Filament\Resources\PurchaseReports\PurchaseReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseReport extends ViewRecord
{
    protected static string $resource = PurchaseReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
