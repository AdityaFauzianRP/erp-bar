<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Mail\UserCreatedMail;
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

        try {
            if ($this->plainPassword) {
                Mail::to($user->email)->send(new UserCreatedMail($user, $this->plainPassword));
                
                Notification::make()
                    ->title('Berhasil')
                    ->body('User dibuat dan email detail login telah dikirim.')
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Email Gagal Terkirim')
                ->body('User tersimpan, namun email gagal dikirim: ' . $e->getMessage())
                ->warning()
                ->persistent()
                ->send();
        }
    }
}