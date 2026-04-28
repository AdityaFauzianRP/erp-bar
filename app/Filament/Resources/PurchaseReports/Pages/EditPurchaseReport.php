<?php

namespace App\Filament\Resources\PurchaseReports\Pages;

use App\Filament\Resources\PurchaseReports\PurchaseReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseReport extends EditRecord
{
    protected static string $resource = PurchaseReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
