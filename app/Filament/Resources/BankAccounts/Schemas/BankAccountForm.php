<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Detail Rekening Bank')
                    ->description('Masukkan informasi rekening resmi perusahaan untuk proses pembayaran.')
                    ->icon('heroicon-o-building-library')
                    ->columns(2)
                    ->schema([
                        TextInput::make('bank_name')
                            ->label('Nama Bank')
                            ->required()
                            ->placeholder('Contoh: BCA, Mandiri, BNI')
                            ->maxLength(255),

                        TextInput::make('bank_code')
                            ->label('Kode Bank')
                            ->placeholder('Contoh: 014')
                            ->numeric()
                            ->maxLength(10),

                        TextInput::make('account_number')
                            ->label('Nomor Rekening')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Masukkan nomor tanpa spasi/titik')
                            ->prefixIcon('heroicon-m-credit-card'),

                        TextInput::make('account_holder')
                            ->label('Atas Nama (Owner)')
                            ->required()
                            ->placeholder('Sesuai yang tertera di buku tabungan')
                            ->prefixIcon('heroicon-m-user'),

                        // TextInput::make('initial_balance')
                        //     ->label('Saldo Awal')
                        //     ->numeric()
                        //     ->prefix('Rp')
                        //     ->default(0)
                        //     ->helperText('Saldo awal saat sistem ini mulai digunakan.'),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->inline(false)
                            ->onColor('success'),
                    ])
                    ->columnSpanFull(),
                    
            ]);
    }
}
