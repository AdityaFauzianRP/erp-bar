<?php

namespace App\Filament\Resources\CustomerInduks\Pages;

use App\Filament\Resources\CustomerInduks\CustomerIndukResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerInduk extends CreateRecord
{
    protected static string $resource = CustomerIndukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan Data');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->hidden();
    }

    protected function afterCreate(): void
    {
        $data = $this->form->getRawState();

        // 1. Simpan Data Brands
        if (isset($data['brands']) && is_array($data['brands'])) {
            foreach ($data['brands'] as $brand) {
                $this->record->brands()->create([
                    'brand_name'  => $brand['brand_name'],
                    'nama_cabang' => $brand['nama_cabang'],
                ]);
            }
        }

        // 2. Simpan Data Harga Khusus Produk (PENAMBAHAN BARU)
        if (isset($data['product_prices']) && is_array($data['product_prices'])) {
            foreach ($data['product_prices'] as $price) {
                $this->record->productPrices()->create([
                    'product_id'    => $price['product_id'],
                    'special_price' => $price['special_price'],
                ]);
            }
        }
    }
}
