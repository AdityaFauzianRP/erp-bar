<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Mail\UserCreatedMail;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public ?string $plainPassword = null;

    protected function afterCreate(): void
    {
        $user = $this->record;

        // 1. Insert ke tabel branch_user secara otomatis (Static Branch ID: 1)
        // Menggunakan syncWithoutDetaching agar tidak duplikat jika proses terpanggil ulang
        $user->branches()->syncWithoutDetaching([1]);

        try {
            if ($this->plainPassword) {
                Mail::to($user->email)->send(new UserCreatedMail($user, $this->plainPassword));
                
                Notification::make()
                    ->title('Berhasil')
                    ->body('User dibuat, dihubungkan ke Cabang Utama, dan email login telah dikirim.')
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Email Gagal Terkirim')
                ->body('User tersimpan di Cabang Utama, namun email gagal dikirim: ' . $e->getMessage())
                ->warning()
                ->persistent()
                ->send();
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