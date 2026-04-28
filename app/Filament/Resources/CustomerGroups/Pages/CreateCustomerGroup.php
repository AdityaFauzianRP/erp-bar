<?php

namespace App\Filament\Resources\CustomerGroups\Pages;

use App\Filament\Resources\CustomerGroups\CustomerGroupResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerGroup extends CreateRecord
{
    protected static string $resource = CustomerGroupResource::class;

        protected function getRedirectUrl(): string
        {
            return $this->getResource()::getUrl('index');
        }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Grup Pelanggan Baru')
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
