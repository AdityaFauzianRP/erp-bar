<?php

namespace App\Filament\Resources\CustomerInduks\Pages;

use App\Filament\Resources\CustomerInduks\CustomerIndukResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerInduk extends EditRecord
{
    protected static string $resource = CustomerIndukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
