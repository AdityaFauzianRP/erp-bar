<?php

namespace App\Filament\Resources\OperationalReports\Pages;

use App\Filament\Resources\OperationalReports\OperationalReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOperationalReport extends ViewRecord
{
    protected static string $resource = OperationalReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
