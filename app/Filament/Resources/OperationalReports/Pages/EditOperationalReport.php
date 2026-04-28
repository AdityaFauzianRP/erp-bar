<?php

namespace App\Filament\Resources\OperationalReports\Pages;

use App\Filament\Resources\OperationalReports\OperationalReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditOperationalReport extends EditRecord
{
    protected static string $resource = OperationalReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
