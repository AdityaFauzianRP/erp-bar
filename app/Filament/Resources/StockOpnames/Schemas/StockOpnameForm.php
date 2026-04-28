<?php

namespace App\Filament\Resources\StockOpnames\Schemas;

use App\Models\Inventory;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Components\Utilities\Set as UtilitiesSet;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class StockOpnameForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                ComponentsSection::make('Informasi Utama')
                    ->description('Detail identitas dokumen, lokasi gudang, dan tanggal pelaksanaan opname.')
                    ->icon('heroicon-s-clipboard-document-check') // Icon solid untuk kesan profesional
                    ->iconColor('primary')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('opname_number')
                                ->label('Nomor Dokumen')
                                ->readOnly()
                                ->prefixIcon('heroicon-m-hashtag')
                                ->prefixIconColor('primary')
                                // Aksen teks biru tebal agar nomor dokumen menonjol
                                ->extraInputAttributes(['class' => 'font-bold text-primary-600 bg-blue-50/50 rounded-xl'])
                                ->default(function () {
                                    $now = now();
                                    $prefix = "PJ"; // Dipersingkat agar tidak terlalu panjang di UI
                                    $year = $now->format('Y');
                                    $month = $now->format('m');
                                    $day = $now->format('d');

                                    $lastRecord = \App\Models\StockOpname::whereYear('created_at', $year)
                                        ->whereMonth('created_at', $month)
                                        ->latest('id')
                                        ->first();

                                    if ($lastRecord && $lastRecord->opname_number) {
                                        $lastCounter = (int) substr($lastRecord->opname_number, -4);
                                        $newCounter = $lastCounter + 1;
                                    } else {
                                        $newCounter = 1;
                                    }

                                    return sprintf(
                                        "%s/%s/%s/%s/%s",
                                        $prefix,
                                        $year,
                                        $month,
                                        $day,
                                        str_pad($newCounter, 4, '0', STR_PAD_LEFT)
                                    );
                                }),

                            Select::make('warehouse_id')
                                ->label('Lokasi Gudang')
                                ->relationship('warehouse', 'name')
                                ->prefixIcon('heroicon-m-home-modern')
                                ->prefixIconColor('primary')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->disabled(fn($record) => $record && $record->status === 'completed')
                                ->afterStateUpdated(function ($set, $state, $operation) {
                                    if ($operation !== 'create' || !$state) return;

                                    $inventoryData = \App\Models\Inventory::where('warehouse_id', $state)
                                        ->with('product')
                                        ->get()
                                        ->mapWithKeys(function ($inv) {
                                            $key = (string) $inv->product_id;
                                            return [$key => [
                                                'product_id' => $inv->product_id,
                                                'product_name' => $inv->product?->name ?? 'Produk Tidak Dikenal',
                                                'system_stock' => (float) $inv->stock,
                                                'physical_stock' => (float) $inv->stock,
                                                'difference' => 0,
                                            ]];
                                        })
                                        ->toArray();

                                    $set('items', $inventoryData);
                                }),

                            DatePicker::make('date')
                                ->label('Tanggal Pelaksanaan')
                                ->prefixIcon('heroicon-m-calendar-days')
                                ->prefixIconColor('primary')
                                ->default(now())
                                ->required()
                                ->disabled(fn($record) => $record && $record->status === 'completed'),

                            Hidden::make('user_id')
                                ->default(auth()->id()),
                        ]),
                    ])
                    ->columnSpanFull()
                    ->collapsible(), // Bisa di-collapse agar hemat ruang jika data item banyak

                ComponentsSection::make('Item Barang')
                    ->schema([
                        ViewField::make('items')
                            ->view('filament.forms.components.stock-opname-repeater')
                            ->columnSpanFull()
                            ->dehydrated(true)
                            ->extraAttributes(fn($record) => [
                                'is_disabled' => $record && $record->status === 'completed'
                            ])
                            ->formatStateUsing(function ($state, $record) {
                                // Jika $state kosong, coba ambil dari relasi record (Mode Edit)
                                $dataItems = $state;
                                if (empty($dataItems) && $record && $record->items) {
                                    $dataItems = $record->items;
                                }

                                if (empty($dataItems)) return [];

                                $items = is_array($dataItems) ? $dataItems : $dataItems->toArray();
                                $formatted = [];

                                foreach ($items as $item) {
                                    $productId = $item['product_id'];

                                    // CARI PRODUCT BESERTA UNITNYA
                                    $product = Product::with('unit')->find($productId);

                                    $productName = $item['product']['name'] ?? ($item['product_name'] ?? $product?->name ?? 'Unknown');
                                    $unitName = $product?->unit?->name ?? '-'; // Ambil dari relasi

                                    $key = (string) $productId;
                                    $formatted[$key] = [
                                        'product_id' => $productId,
                                        'product_name' => $productName,
                                        'unit_name' => $unitName, // Sekarang variabel $unitName sudah ada isinya
                                        'system_stock' => (float) ($item['system_stock'] ?? 0),
                                        'physical_stock' => (float) ($item['physical_stock'] ?? 0),
                                        'difference' => (float) ($item['difference'] ?? 0),
                                        'waster_qty' => (float) ($item['waster_qty'] ?? 0),
                                        'waster_price' => (float) ($item['waster_price'] ?? 0),
                                        'waster_total_price' => (float) ($item['waster_total_price'] ?? 0),
                                    ];
                                }
                                return $formatted;
                                return $formatted;
                            })
                            ->live(),
                    ])
                    ->columnSpanFull(),

                // ->schema([
                //     ViewField::make('items')
                //         ->view('filament.forms.components.stock-opname-repeater')
                //         ->afterStateHydrated(function ($component, $state, $record) {
                //             $dataItems = $state;
                //             if ($record && empty($state)) {
                //                 $dataItems = $record->items;
                //             }

                //             if (empty($dataItems)) return [];

                //             $items = is_array($dataItems) ? $dataItems : $dataItems->toArray();
                //             $formatted = [];

                //             foreach ($items as $item) {
                //                 $productId = $item['product_id'];
                //                 $productName = $item['product']['name'] ?? ($item['product_name'] ?? Product::find($productId)?->name ?? 'Unknown');

                //                 $key = (string) $productId;
                //                 $formatted[$key] = [
                //                     'product_id' => $productId,
                //                     'product_name' => $productName,
                //                     'system_stock' => (float) ($item['system_stock'] ?? 0),
                //                     'physical_stock' => (float) ($item['physical_stock'] ?? 0),
                //                     'difference' => (float) ($item['difference'] ?? 0),
                //                     // TAMBAHKAN 3 FIELD BARU INI

                //                 ];
                //             }
                //             return $formatted;
                //         })
                //         ->live(),
                // ])
                // ->columnSpanFull(),
            ]);
    }
}
