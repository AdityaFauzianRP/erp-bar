<?php

namespace App\Filament\Resources\OperationalProducts\Pages;

use App\Filament\Resources\OperationalProducts\OperationalProductResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditOperationalProduct extends EditRecord
{
    protected static string $resource = OperationalProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan Produk Operational')
                ->submit('save')
                ->color('primary')
                ->icon('heroicon-m-check-circle')
                ->keyBindings(['mod+s']),

            Action::make('cancel')
                ->label('Batal')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
