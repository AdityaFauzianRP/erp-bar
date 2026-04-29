<?php

namespace App\Filament\Resources\SchoolCategories\Pages;

use App\Filament\Resources\SchoolCategories\SchoolCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSchoolCategories extends ListRecords
{
    protected static string $resource = SchoolCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
