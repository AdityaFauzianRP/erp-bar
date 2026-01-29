<?php

namespace App\Filament\Resources\CustomerInduks\Schemas;

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Components\Tabs as ComponentsTabs;
use Filament\Schemas\Components\Tabs\Tab as TabsTab;
use Filament\Schemas\Schema;

class CustomerIndukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                ComponentsTabs::make('Customer Details')
                    ->tabs([
                        // TAB 1: PROFIL & KONTAK
                        TabsTab::make('Profil & Kontak')
                            ->icon('heroicon-m-user-circle')
                            ->schema([
                                ComponentsSection::make('Identitas Perusahaan')
                                    ->schema([
                                        ComponentsGrid::make(2)->schema([
                                            TextInput::make('code')
                                                ->label('Kode Customer')
                                                ->disabled()
                                                ->placeholder('Otomatis')
                                                ->prefixIcon('heroicon-m-qr-code'),

                                            TextInput::make('name')
                                                ->label('Nama Perusahaan')
                                                ->required()
                                                ->prefixIcon('heroicon-m-building-office'),

                                            TextInput::make('alias')
                                                ->label('Nama Alias')
                                                ->placeholder('Nama beken/singkatan'),

                                            TextInput::make('tax_id')
                                                ->label('NPWP (Tax ID)')
                                                ->mask('99.999.999.9-999.999')
                                                ->prefixIcon('heroicon-m-credit-card'),
                                        ]),
                                    ]),

                                ComponentsSection::make('Komunikasi & Alamat')
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
                                        ]),

                                        ComponentsGrid::make(2)->schema([
                                            Textarea::make('head_office_address')
                                                ->label('Alamat Kantor Pusat')
                                                ->rows(3)
                                                ->placeholder('Jl. Contoh No. 123...'),

                                            Textarea::make('tax_address')
                                                ->label('Alamat Faktur Pajak')
                                                ->rows(3)
                                                ->placeholder('Kosongkan jika sama dengan kantor pusat'),
                                        ]),
                                    ]),
                            ]),

                        // TAB 2: KEBIJAKAN BISNIS
                        TabsTab::make('Kebijakan Operasional')
                            ->icon('heroicon-m-cog-6-tooth')
                            ->schema([
                                ComponentsSection::make('Parameter Transaksi')
                                    ->description('Atur term of payment dan status perpajakan.')
                                    ->schema([
                                        ComponentsGrid::make(2)->schema([
                                            TextInput::make('term_of_payment')
                                                ->label('Term of Payment')
                                                ->numeric()
                                                ->suffix('Durasi')
                                                ->prefixIcon('heroicon-m-calendar-days'),

                                            Select::make('top_unit')
                                                ->label('Satuan Waktu')
                                                ->options([
                                                    'day' => 'Hari',
                                                    'week' => 'Minggu',
                                                    'month' => 'Bulan',
                                                ])
                                                ->native(false),
                                        ]),
                                    ]),

                                ComponentsSection::make('Status & Harga')
                                    ->schema([
                                        ComponentsGrid::make(3)->schema([
                                            Toggle::make('use_custom_price')
                                                ->label('Harga Khusus')
                                                ->helperText('Gunakan price list unik')
                                                ->inline(false),

                                            Toggle::make('is_taxable')
                                                ->label('Kena PPN')
                                                ->default(true)
                                                ->inline(false),

                                            Toggle::make('is_active')
                                                ->label('Status Aktif')
                                                ->default(true)
                                                ->inline(false),
                                        ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(), // Bagus agar saat refresh tab tidak balik ke awal
            ]);
    }
}