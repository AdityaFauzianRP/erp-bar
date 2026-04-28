<?php

namespace App\Filament\Resources\SalesDetailReports\Pages;

use App\Filament\Resources\SalesDetailReports\SalesDetailReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSalesDetailReport extends EditRecord
{
    protected static string $resource = SalesDetailReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
