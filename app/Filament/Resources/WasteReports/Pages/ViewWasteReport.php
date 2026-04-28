<?php

namespace App\Filament\Resources\WasteReports\Pages;

use App\Filament\Resources\WasteReports\WasteReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWasteReport extends ViewRecord
{
    protected static string $resource = WasteReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
