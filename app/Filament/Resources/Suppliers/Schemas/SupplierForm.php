<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Components\Tabs as ComponentsTabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                ComponentsTabs::make('Supplier Details')
                    ->tabs([
                        // TAB 1: PROFIL & KONTAK
                        Tab::make('Profil & Kontak')
                            ->icon('heroicon-m-building-storefront')
                            ->schema([
                                ComponentsSection::make('Identitas Supplier')
                                    ->description('Informasi dasar mengenai perusahaan penyuplai.')
                                    ->schema([
                                        ComponentsGrid::make(2)->schema([
                                            TextInput::make('code')
                                                ->label('Kode Supplier')
                                                ->placeholder('Otomatis (SUP-xxxx)')
                                                ->disabled()
                                                ->prefixIcon('heroicon-m-qr-code'),

                                            TextInput::make('name')
                                                ->label('Nama Supplier / Perusahaan')
                                                ->required()
                                                ->placeholder('Contoh: PT. Informa Furnishing')
                                                ->prefixIcon('heroicon-m-building-office'),

                                            TextInput::make('pic')
                                                ->label('Nama PIC (Contact Person)')
                                                ->placeholder('Nama orang yang bisa dihubungi')
                                                ->prefixIcon('heroicon-m-user'),

                                            TextInput::make('phone')
                                                ->label('Nomor Telepon / WA')
                                                ->tel()
                                                ->placeholder('0812xxxx')
                                                ->prefixIcon('heroicon-m-phone'),
                                        ]),

                                        Textarea::make('address')
                                            ->label('Alamat Kantor / Gudang')
                                            ->rows(3)
                                            ->placeholder('Jl. Nama Jalan No. 123...')
                                            ->columnSpanFull(),

                                        Select::make('branches')
                                            ->label('Cabang yang Dilayani')
                                            ->relationship('branches', 'name') // Mengacu pada fungsi branches() di model
                                            ->multiple() // User bisa pilih lebih dari satu cabang
                                            ->preload()
                                            ->searchable()
                                            ->placeholder('Pilih cabang...')
                                            ->helperText('Hanya cabang yang terpilih yang dapat melihat supplier ini.')
                                            ->required(),
                                    ]),
                            ]),

                        // TAB 2: INFORMASI KEUANGAN
                        Tab::make('Informasi Pembayaran')
                            ->icon('heroicon-m-credit-card')
                            ->schema([
                                ComponentsSection::make('Rekening Bank Supplier')
                                    ->description('Data ini digunakan untuk keperluan pembayaran tagihan (Purchasing).')
                                    ->schema([
                                        ComponentsGrid::make(2)->schema([
                                            TextInput::make('bank_name')
                                                ->label('Nama Bank')
                                                ->placeholder('Contoh: BCA / Mandiri / BRI')
                                                ->prefixIcon('heroicon-m-banknotes'),

                                            TextInput::make('bank_account_number')
                                                ->label('Nomor Rekening')
                                                ->placeholder('Masukkan angka rekening')
                                                ->prefixIcon('heroicon-m-credit-card'),
                                        ]),
                                    ]),

                                ComponentsSection::make('Pengaturan Status')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Status Supplier Aktif')
                                            ->helperText('Matikan jika supplier tidak lagi bekerja sama')
                                            ->default(true)
                                            ->inline(false),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
}
