<?php

namespace App\Filament\Resources\OperationalItemReports\Pages;

use App\Filament\Resources\OperationalItemReports\OperationalItemReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOperationalItemReport extends ViewRecord
{
    protected static string $resource = OperationalItemReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
