<?php

namespace App\Filament\Resources\AssetCategories\Pages;

use App\Filament\Resources\AssetCategories\AssetCategoryResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetCategory extends CreateRecord
{
    protected static string $resource = AssetCategoryResource::class;

    // Matikan tombol 'Create & Create Another'
    protected static bool $canCreateAnother = false;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Simpan Kategori')
                ->submit('create')
                ->color('primary')
                ->icon('heroicon-m-plus-circle')
                ->keyBindings(['mod+s']),

            Action::make('cancel')
                ->label('Batal')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}