<?php

namespace App\Filament\Resources\Purchases\Pages;

use App\Filament\Resources\Purchases\PurchaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPurchases extends ListRecords
{
    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Pembelian Baru'),

            CreateAction::make('directPurchase')
                ->label('Pembelian Khusus (Direct)')
                ->color('success')
                ->icon('heroicon-o-bolt')
                ->url(fn(): string => static::$resource::getUrl('direct-purchase')),
        ];
    }
}
