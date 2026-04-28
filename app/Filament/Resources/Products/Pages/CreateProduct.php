<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Inventory;
use App\Models\Warehouse;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan Data ');
    }


    // 2. Menghilangkan Tombol "Create & Create Another"
    protected function getCreateAnotherFormAction(): Action
    {
        // Kita buat action kosong (empty) agar tidak muncul di view
        return parent::getCreateAnotherFormAction()
            ->hidden();
    }

    protected function afterCreate(): void
    {
        $product = $this->record;

        // 1. Ambil semua ID Gudang dari table warehouses
        $warehouses = Warehouse::all();

        // 2. Tambahkan produk ke setiap gudang di table inventories
        foreach ($warehouses as $warehouse) {
            Inventory::create([
                'warehouse_id' => $warehouse->id,
                'product_id'   => $product->id,
                'branch_id'    => 1, // Default sesuai permintaan
                'stock'        => 0,
                'min_stock'    => 0,
            ]);
        }
    }
}
