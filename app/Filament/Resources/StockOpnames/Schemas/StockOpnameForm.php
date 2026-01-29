<?php

namespace App\Filament\Resources\StockOpnames\Schemas;

use App\Models\Inventory; // Tambahkan ini
use Filament\Forms;        // Tambahkan ini
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;   // Filament v3 menggunakan Form, bukan Schema
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use Filament\Schemas\Components\Utilities\Set;

class StockOpnameForm
{
    public static function configure(Schema $form): Schema // Ubah Schema jadi Form
    {
        return $form
            ->schema([ // Method utamanya adalah schema
                Section::make('Informasi Utama')
                    ->schema([
                        TextInput::make('opname_number')
                            ->label('Nomor Dokumen')
                            ->default(fn() => 'SO-' . date('Ymd') . '-' . strtoupper(str()->random(4)))
                            ->readOnly() // Gunakan readOnly() untuk konsistensi
                            ->required(),

                        Select::make('warehouse_id')
                            ->label('Gudang')
                            ->relationship('warehouse', 'name')
                            ->required()
                            ->live(), // Di v3, live() lebih disarankan daripada reactive()

                        DatePicker::make('date')
                            ->label('Tanggal Opname')
                            ->default(now())
                            ->required(),

                        Hidden::make('user_id')
                            ->default(Auth::id()),
                    ])->columns(3)
                    ->columnSpanFull(),

                Section::make('Item Barang')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('branch_id')
                                    ->label('Pemilik (PT)')
                                    ->relationship('branch', 'name')
                                    ->required()
                                    ->live(),

                                // Tambahkan di dalam repeater items
                                // Di dalam file StockOpnameForm.php

                                Select::make('product_id')
                                    ->label('Produk')
                                    ->relationship('product', 'name')
                                    /** * Trik Utama: Menggabungkan Nama Produk dan Nama Unit di Dropdown
                                     * Kita panggil relasi unit untuk mengambil kolom 'name' dari tabel units
                                     */
                                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} - {$record->unit?->name}")
                                    ->searchable()
                                    ->preload() // Memuat data lebih cepat saat diklik
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        // 1. Ambil data produk beserta unitnya (Eager Load)
                                        $product = \App\Models\Product::with('unit')->find($state);

                                        // 2. Set Nama Unit ke field 'unit_name'
                                        $unitName = $product?->unit?->name ?? '-';
                                        $set('unit_name', $unitName);

                                        // 3. Ambil stok sistem dari tabel Inventory
                                        $warehouseId = $get('../../warehouse_id');
                                        $branchId = $get('branch_id');

                                        if ($state && $warehouseId && $branchId) {
                                            $inv = \App\Models\Inventory::where([
                                                'warehouse_id' => $warehouseId,
                                                'product_id' => $state,
                                                'branch_id' => $branchId
                                            ])->first();

                                            $set('system_stock', $inv ? $inv->stock : 0);

                                            // 4. Hitung ulang selisih
                                            $physical = (float)($get('physical_stock') ?? 0);
                                            $system = $inv ? (float)$inv->stock : 0;
                                            $set('difference', $physical - $system);
                                        }
                                    }),

                                
                                TextInput::make('system_stock')
                                    ->label('Stok Sistem')
                                    ->numeric()
                                    ->readOnly()
                                    ->default(0),

                                TextInput::make('physical_stock')
                                    ->label('Stok Fisik')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true) // Hitung selisih setelah user selesai ngetik
                                    ->afterStateUpdated(fn($state, Set $set, Get $get)
                                    => $set('difference', (float)$state - (float)$get('system_stock'))),
                                
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull(),
            ]);
    }
}
