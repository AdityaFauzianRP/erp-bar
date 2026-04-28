<?php

namespace App\Filament\Resources\WasteReports\Pages;

use App\Filament\Resources\WasteReports\WasteReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditWasteReport extends EditRecord
{
    protected static string $resource = WasteReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
