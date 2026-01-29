<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select; // Tambahkan ini
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.auth.edit-profile';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Profil & Pengaturan Cabang';

    public ?array $data = [];

    public function mount(): void
    {
        // Mengisi form dengan data user + active_branch_id
        $this->form->fill([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'active_branch_id' => auth()->user()->active_branch_id,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // --- BAGIAN CABANG AKTIF ---
                ComponentsSection::make('Lokasi Kerja')
                    ->description('Pilih cabang mana yang ingin Anda aktifkan saat ini.')
                    ->aside()
                    ->schema([
                        Select::make('active_branch_id')
                            ->label('Cabang Aktif')
                            ->options(fn () => auth()->user()->branches->pluck('name', 'id'))
                            ->helperText('Semua transaksi yang Anda buat akan dicatat pada cabang ini.')
                            ->required()
                            ->native(false) // Tampilan lebih modern
                            ->searchable(),
                    ]),

                // --- INFORMASI PRIBADI ---
                ComponentsSection::make('Informasi Pribadi')
                    ->description('Perbarui nama dan alamat email Anda.')
                    ->aside()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required(),
                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),

                // --- KEAMANAN ---
                ComponentsSection::make('Keamanan')
                    ->description('Pastikan akun Anda menggunakan password yang kuat.')
                    ->aside()
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Password Saat Ini')
                            ->password()
                            ->required()
                            ->currentPassword(),
                        TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->rule(Password::default())
                            ->helperText('Kosongkan jika tidak ingin mengubah password.'),
                        TextInput::make('new_password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password()
                            ->same('new_password')
                            ->requiredWith('new_password'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'active_branch_id' => $data['active_branch_id'], // Simpan cabang aktif
        ];

        if (filled($data['new_password'])) {
            $updateData['password'] = Hash::make($data['new_password']);
        }

        $user->update($updateData);

        Notification::make()
            ->success()
            ->title('Profil & Cabang Berhasil Diperbarui')
            ->body('Data Anda dan pengaturan cabang telah disimpan.')
            ->send();

        // Refresh form agar password tidak nyangkut tapi cabang tetap terpilih
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'active_branch_id' => $user->active_branch_id,
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->color('primary') // Pakai primary agar lebih menonjol
                ->submit('save'),
        ];
    }
}