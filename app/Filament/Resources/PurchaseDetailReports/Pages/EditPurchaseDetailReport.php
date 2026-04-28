<?php

namespace App\Filament\Resources\PurchaseDetailReports\Pages;

use App\Filament\Resources\PurchaseDetailReports\PurchaseDetailReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseDetailReport extends EditRecord
{
    protected static string $resource = PurchaseDetailReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
