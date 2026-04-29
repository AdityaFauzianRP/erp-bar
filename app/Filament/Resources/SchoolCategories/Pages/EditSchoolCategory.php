<?php

namespace App\Filament\Resources\SchoolCategories\Pages;

use App\Filament\Resources\SchoolCategories\SchoolCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSchoolCategory extends EditRecord
{
    protected static string $resource = SchoolCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
