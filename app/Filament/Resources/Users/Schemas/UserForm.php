<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Profil')
                    ->description('Data utama akun pengguna.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->placeholder('Masukkan nama lengkap...')
                            ->prefixIcon('heroicon-m-user'),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-m-envelope'),

                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->required(fn(string $context): bool => $context === 'create')
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(function ($state, $livewire) {
                                if ($livewire instanceof \Filament\Resources\Pages\CreateRecord) {
                                    $livewire->plainPassword = $state;
                                }
                                return \Illuminate\Support\Facades\Hash::make($state);
                            })
                            ->placeholder('••••••••')
                            ->prefixIcon('heroicon-m-lock-closed'),
                    ])
                    ->columnSpan(2),

                Section::make('Otoritas')
                    ->description('Pengaturan hak akses.')
                    ->schema([
                        Select::make('roles')
                            ->label('Peran (Role)')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->native(false),

                        // Select::make('branches')
                        //     ->label('Kantor Cabang')
                        //     // Kita buat static options, kunci di satu ID cabang saja
                        //     ->options([
                        //         '1' => 'PT BAR Tech (Pusat)', // Sesuaikan ID dan Nama Cabangnya
                        //     ])
                        //     // Karena tadi pakai multiple(), kita tetap pertahankan jika memang relasi di DB-nya belongsToMany
                        //     ->multiple()
                        //     ->default(['1']) // Langsung terpilih otomatis
                        //     ->selectablePlaceholder(false) // Menghilangkan pilihan kosong
                        //     ->disabled() // Opsional: Tambahkan ini jika user tidak boleh menggantinya sama sekali
                        //     ->dehydrated() // Penting: Agar nilai yang di-disable tetap terkirim saat save
                        //     ->helperText('User ini dikunci hanya untuk akses di kantor pusat.')
                        //     ->visibleOn('create'),

                        // // Munculkan Placeholder (Teks statis) hanya saat Edit
                        // Placeholder::make('branch_info')
                        //     ->label('Kantor Cabang')
                        //     ->content('PT BAR Tech (Pusat)')
                        //     ->visibleOn('edit'),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }
}
