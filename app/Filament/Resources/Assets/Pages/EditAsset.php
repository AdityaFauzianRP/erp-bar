<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    /**
     * Mengatur Header Actions (Tombol di pojok kanan atas halaman)
     */

    /**
     * Mengatur Form Actions (Tombol di bagian bawah form)
     */
    protected function getFormActions(): array
    {
        return [
            // Tombol Simpan Utama
            Action::make('save')
                ->label('Simpan Perubahan Asset') // Label kustom sesuai request
                ->submit('save') // Memicu proses update data
                ->color('primary')
                ->icon('heroicon-m-check-circle') // Menambah icon agar lebih keren
                ->keyBindings(['mod+s']),

            // Tombol Batal/Kembali
            Action::make('cancel')
                ->label('Batal')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    /**
     * Opsional: Redirect setelah simpan
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}