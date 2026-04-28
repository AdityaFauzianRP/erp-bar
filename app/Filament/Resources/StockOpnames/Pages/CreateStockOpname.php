<?php

namespace App\Filament\Resources\StockOpnames\Pages;

use App\Filament\Resources\StockOpnames\StockOpnameResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateStockOpname extends CreateRecord
{
    protected static string $resource = StockOpnameResource::class;

    // Kita simpan items sementara di variabel class agar bisa diakses di afterCreate
    protected array $savedItems = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['items'])) {
            // Field waster_qty dll sudah ada di dalam array ini otomatis dari Blade
            $this->savedItems = array_values($data['items']);
            unset($data['items']);
        }
        return $data;
    }

    // protected function afterCreate(): void
    // {
    //     if (!empty($this->savedItems)) {
    //         $this->record->items()->createMany($this->savedItems);
    //     }
    // }

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Simpan manual ke tabel detail/relasi
        if (!empty($this->savedItems)) {
            $record->items()->createMany($this->savedItems);
            Log::info('Berhasil simpan detail item untuk ID: ' . $record->id);
        }
    }

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
}