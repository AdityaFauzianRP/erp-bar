<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // BAGIAN 1: IDENTITAS SUPPLIER (BLUE ACCENT)
                ComponentsSection::make('Identitas Supplier')
                    ->description('Informasi dasar dan kontak perusahaan penyuplai.')
                    ->icon('heroicon-m-building-storefront')
                    ->iconColor('primary') // Akan mengikuti warna tema Filament (Biru)
                    ->collapsible()
                    ->schema([
                        ComponentsGrid::make(3)->schema([
                            TextInput::make('code')
                                ->label('Kode Supplier')
                                ->placeholder('Otomatis (SUP-xxxx)')
                                ->disabled()
                                ->dehydrated(false)
                                ->prefixIcon('heroicon-m-qr-code')
                                ->extraInputAttributes(['style' => 'background-color: #f0f9ff']), // Biru sangat muda

                            TextInput::make('name')
                                ->label('Nama Perusahaan')
                                ->required()
                                ->placeholder('Contoh: PT. Maju Jaya')
                                ->prefixIcon('heroicon-m-building-office')
                                ->prefixIconColor('primary')
                                ->columnSpan(2),

                            TextInput::make('pic')
                                ->label('Nama PIC')
                                ->placeholder('Nama person-in-charge')
                                ->prefixIcon('heroicon-m-user')
                                ->prefixIconColor('primary'),

                            TextInput::make('phone')
                                ->label('Nomor Telepon')
                                ->tel()
                                ->placeholder('0812xxxx')
                                ->prefixIcon('heroicon-m-phone')
                                ->prefixIconColor('primary'),

                            Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true)
                                ->onColor('success')
                                ->offColor('danger')
                                ->inline(false),
                        ]),

                        Textarea::make('address')
                            ->label('Alamat Kantor')
                            ->rows(2)
                            ->placeholder('Jl. Alamat Lengkap...')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'border-t-4 border-t-blue-600 shadow-sm']), // Garis atas biru tebal

                // BAGIAN 2: KATALOG PRODUK (TABEL KUSTOM)
                ComponentsSection::make('Katalog Produk')
                    ->description('Kelola daftar harga beli khusus dari supplier ini dalam satu tabel.')
                    ->icon('heroicon-m-shopping-bag')
                    ->iconColor('primary')
                    ->schema([
                        ViewField::make('product_suppliers')
                            ->view('filament.forms.components.supplier-catalog-table')
                            ->columnSpanFull()
                            ->dehydrated(true)
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $items = $record->product_suppliers()->with('product')->get()->map(function ($item) {
                                        return [
                                            'product_id' => $item->product_id,
                                            'product_name' => $item->product?->name ?? 'N/A',
                                            'sku_supplier' => $item->sku_supplier,
                                            'harga_beli_khusus' => $item->harga_beli_khusus,
                                            'branch_id' => $item->branch_id,
                                        ];
                                    })->toArray();
                                    $component->state($items);
                                }
                            })
                            ->saveRelationshipsUsing(function ($component, $state) {
                                $supplier = $component->getRecord();
                                if (! $supplier) return;

                                $supplier->product_suppliers()->delete();

                                foreach ($state as $item) {
                                    $supplier->product_suppliers()->create([
                                        'product_id' => $item['product_id'],
                                        'sku_supplier' => $item['sku_supplier'] ?? null,
                                        'harga_beli_khusus' => $item['harga_beli_khusus'] ?? 0,
                                        'branch_id' => 1,
                                    ]);
                                }
                            }),
                    ])
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'border-t-4 border-t-blue-600 shadow-sm']),
            ]);
    }
}
