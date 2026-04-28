<?php

namespace App\Filament\Resources\CustomerInduks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;

class CustomerIndukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // SECTION 1: IDENTITAS UTAMA
                ComponentsSection::make('Identitas Perusahaan')
                    ->description('Informasi dasar dan legalitas customer.')
                    ->icon('heroicon-m-building-office')
                    ->collapsible()
                    ->schema([
                        ComponentsGrid::make(3)->schema([
                            TextInput::make('code')
                                ->label('Kode Customer')
                                ->disabled()
                                ->placeholder('Otomatis')
                                ->prefixIcon('heroicon-m-qr-code'),

                            TextInput::make('name')
                                ->label('Nama Perusahaan')
                                ->required()
                                ->prefixIcon('heroicon-m-building-office-2'),

                            TextInput::make('alias')
                                ->label('Nama Alias')
                                ->placeholder('Contoh: PT. MJ'),

                            Select::make('customer_group_id')
                                ->label('Grup Pelanggan')
                                ->relationship('customerGroup', 'name')
                                ->searchable()
                                ->preload()
                                ->prefixIcon('heroicon-m-user-group'),

                            TextInput::make('tax_id')
                                ->label('NPWP (Tax ID)')
                                ->mask('99.999.999.9-999.999')
                                ->prefixIcon('heroicon-m-credit-card'),
                        ]),
                    ])
                    ->columnSpanFull(),

                // SECTION 2: KOMUNIKASI & ALAMAT
                ComponentsSection::make('Komunikasi & Alamat')
                    ->icon('heroicon-m-map-pin')
                    ->collapsible()
                    ->schema([
                        ComponentsGrid::make(2)->schema([
                            TextInput::make('email')
                                ->email()
                                ->label('Email Official')
                                ->prefixIcon('heroicon-m-envelope'),

                            TextInput::make('phone')
                                ->tel()
                                ->label('Nomor Telepon')
                                ->prefixIcon('heroicon-m-phone'),

                            TextInput::make('head_office_address')
                                ->label('Alamat Kantor')
                                ->columnSpanFull(),

                        ]),
                    ])
                    ->columnSpanFull(),

                // SECTION 3: TABLE CUSTOMER BRANDS (REPEATER CUSTOM VIEW)
                ComponentsSection::make('Daftar Brand per Cabang')
                    ->schema([
                        ViewField::make('brands')
                            ->view('filament.components.brand-table-repeater')
                            ->dehydrated(false) // Mencegah Filament mencoba simpan ke kolom 'brands' di DB
                            ->afterStateHydrated(function ($set, $record) {
                                if ($record) {
                                    // Ambil data dari relasi brand dan masukkan ke state view
                                    $set('brands', $record->brands()->get(['id', 'brand_name', 'nama_cabang', 'kota_cabang'])->toArray());
                                }
                            })
                    ])
                    ->columnSpanFull(),

                ComponentsSection::make('Harga Khusus Produk')
                    ->description('Atur harga spesial produk untuk customer ini.')
                    ->schema([
                        // Update bagian ViewField product_prices
                        ViewField::make('product_prices')
                            ->view('filament.components.product-price-table')
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($set, $record) {
                                if ($record) {
                                    $set('product_prices', $record->productPrices()
                                        ->with(['product.unit']) // Load relasi product dan unitnya
                                        ->get()
                                        ->map(fn($item) => [
                                            'id' => $item->id,
                                            'product_id' => $item->product_id,
                                            // Gabungkan Nama - Unit untuk ditampilkan di tabel
                                            'product_display_name' => $item->product?->name . ' - ' . ($item->product?->unit?->name ?? '-'),
                                            'special_price' => $item->special_price,
                                        ])->toArray());
                                }
                            }),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
