<?php

namespace App\Filament\Resources\StockOpnames\Pages;

use App\Filament\Resources\StockOpnames\StockOpnameResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditStockOpname extends EditRecord
{
    protected static string $resource = StockOpnameResource::class;

    protected array $savedItems = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Data Opname')
                // Tombol akan disembunyikan jika status adalah 'completed'
                ->hidden(fn() => $this->record->status === 'completed'),

            // Atau bisa juga menggunakan visible (hanya muncul jika draft)
            // ->visible(fn () => $this->record->status === 'draft'),
        ];
    }

    /**
     * Memastikan relasi dimuat saat record diambil dari database.
     * Tipe data int|string dan Model harus sesuai dengan Filament\Resources\Pages\EditRecord
     */
    protected function resolveRecord(int|string $key): Model
    {
        $record = static::getResource()::getModel()::with(['items.product'])->findOrFail($key);

        return $record;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['items'])) {
            $this->savedItems = array_values($data['items']);
            unset($data['items']);
        }
        return $data;
    }

    protected function afterSave(): void
    {
        if (!empty($this->savedItems)) {
            // Hapus detail lama dan masukkan yang baru (termasuk kolom wasted)
            $this->record->items()->delete();
            $this->record->items()->createMany($this->savedItems);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Simpan Perubahan'), // Mengubah "Save changes"

            $this->getCancelFormAction()
                ->label('Batalkan Perubahan'), // Mengubah "Cancel"
        ];
    }
}
