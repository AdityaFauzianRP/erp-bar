<?php

namespace App\Filament\Resources\ExecSummaryFacts\Pages;

use App\Filament\Resources\ExecSummaryFacts\ExecSummaryFactResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditExecSummaryFact extends EditRecord
{
    protected static string $resource = ExecSummaryFactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
