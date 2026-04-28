<?php

namespace App\Filament\Resources\CustomerInduks\Pages;

use App\Filament\Resources\CustomerInduks\CustomerIndukResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditCustomerInduk extends EditRecord
{
    protected static string $resource = CustomerIndukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // 1. Ambil data brand
        $data['brands'] = $this->record->brands()
            ->get(['brand_name', 'nama_cabang', 'kota_cabang'])
            ->toArray();

        // 2. Ambil data harga produk dengan join unit untuk tampilan display (PENAMBAHAN BARU)
        $data['product_prices'] = $this->record->productPrices()
            ->with(['product.unit'])
            ->get()
            ->map(fn($item) => [
                'product_id'           => $item->product_id,
                'product_display_name' => $item->product?->name . ' - ' . ($item->product?->unit?->name ?? '-'),
                'special_price'        => $item->special_price,
            ])
            ->toArray();

        return $data;
    }

    protected function afterSave(): void
{
    $data = $this->form->getRawState();

    // 1. Sync Data Brands
    if (isset($data['brands']) && is_array($data['brands'])) {
        foreach ($data['brands'] as $brand) {
            $this->record->brands()->updateOrCreate(
                [
                    // Gunakan ID sebagai kunci utama jika ada
                    'id' => $brand['id'] ?? null, 
                ],
                [
                    'brand_name'  => $brand['brand_name'],
                    'nama_cabang' => $brand['nama_cabang'],
                    'kota_cabang' => $brand['kota_cabang'],
                ]
            );
        }
    }

    // 2. Sync Data Harga Produk (Solusi Error 1062)
    if (isset($data['product_prices']) && is_array($data['product_prices'])) {
        foreach ($data['product_prices'] as $price) {
            $this->record->productPrices()->updateOrCreate(
                [
                    // Eloquent otomatis menambahkan customer_induk_id dari relasi $this->record
                    // Jadi Anda cukup mengunci berdasarkan product_id
                    'product_id' => $price['product_id'], 
                ],
                [
                    'special_price' => $price['special_price'],
                ]
            );
        }
    }
}

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan Customer Induk')
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
