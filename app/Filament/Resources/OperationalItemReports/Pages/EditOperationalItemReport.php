<?php

namespace App\Filament\Resources\OperationalItemReports\Pages;

use App\Filament\Resources\OperationalItemReports\OperationalItemReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditOperationalItemReport extends EditRecord
{
    protected static string $resource = OperationalItemReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
