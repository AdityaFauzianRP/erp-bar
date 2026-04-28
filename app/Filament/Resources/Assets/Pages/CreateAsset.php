<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    // 1. Menghilangkan tombol 'Create & Create Another'
    protected static bool $canCreateAnother = false;

    /**
     * 2. Kustomisasi tombol di bagian bawah form (Actions)
     */
    protected function getFormActions(): array
    {
        return [
            // Tombol Simpan (Utama)
            Action::make('create')
                ->label('Simpan Asset Baru') // Label kustom
                ->submit('create') // Memicu proses create bawaan Filament
                ->color('primary')
                ->icon('heroicon-m-plus-circle')
                ->keyBindings(['mod+s']),

            // Tombol Batal
            Action::make('cancel')
                ->label('Batal')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    /**
     * Opsional: Redirect ke halaman daftar (Index) setelah berhasil simpan
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}