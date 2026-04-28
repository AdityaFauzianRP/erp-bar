<?php

namespace App\Filament\Resources\Suppliers\Pages;

use App\Filament\Resources\Suppliers\SupplierResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;

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
        $data = $this->form->getRawState();
        $supplier = $this->record;

        if (isset($data['product_suppliers']) && is_array($data['product_suppliers'])) {
            foreach ($data['product_suppliers'] as $item) {
                // Gunakan updateOrCreate untuk menghindari UniqueConstraintViolationException
                $supplier->product_suppliers()->updateOrCreate(
                    [
                        // Kolom-kolom di bawah ini adalah "Kunci Unik" yang dicek di database
                        'product_id' => $item['product_id'],
                        'branch_id'  => 1, // Jika branch_id selalu 1
                        'supplier_id' => $supplier->id,
                    ],
                    [
                        // Kolom di bawah ini adalah data yang akan di-update atau di-isi saat create
                        'sku_supplier'      => $item['sku_supplier'] ?? null,
                        'harga_beli_khusus' => $item['harga_beli_khusus'] ?? 0,
                    ]
                );
            }
        }
    }
}
