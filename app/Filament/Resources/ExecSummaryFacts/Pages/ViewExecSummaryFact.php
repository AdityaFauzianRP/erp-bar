<?php

namespace App\Filament\Resources\ExecSummaryFacts\Pages;

use App\Filament\Resources\ExecSummaryFacts\ExecSummaryFactResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExecSummaryFact extends ViewRecord
{
    protected static string $resource = ExecSummaryFactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
