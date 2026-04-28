<?php

namespace App\Filament\Resources\ReceivingReports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get as UtilitiesGet;
use Filament\Schemas\Components\Utilities\Set as UtilitiesSet;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;



class ReceivingReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //Grid::make(3)

                Section::make('Informasi Utama')
                    ->description('Detail referensi PO dan nomor dokumen penerimaan barang.')
                    ->icon('heroicon-s-document-check') // Solid icon untuk kesan lebih tegas
                    ->iconColor('primary') // Warna biru default Filament (Primary)
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('receive_number')
                                ->label('No. Penerimaan')
                                ->default(function () {
                                    $prefix = 'GRN';
                                    $date = now();
                                    $year = $date->format('Y');
                                    $month = $date->format('m');
                                    $day = $date->format('d');

                                    // Format dasar untuk pencarian (GR/Tahun/Bulan/)
                                    $searchPattern = "{$prefix}/{$year}/{$month}/%";

                                    // Cari nomor terakhir di bulan ini
                                    $lastRecord = \App\Models\ReceivingReport::where('receive_number', 'like', $searchPattern)
                                        ->latest('id')
                                        ->first();

                                    if ($lastRecord) {
                                        // Ambil 4 angka terakhir dari string (misal dari GR/2026/02/05/0001 ambil 0001)
                                        $lastNumber = (int) substr($lastRecord->receive_number, -4);
                                        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                                    } else {
                                        // Jika belum ada transaksi di bulan ini, mulai dari 0001
                                        $newNumber = '0001';
                                    }

                                    return "{$prefix}/{$year}/{$month}/{$day}/{$newNumber}";
                                })
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->prefixIcon('heroicon-m-hashtag')
                                ->prefixIconColor('primary')
                                ->extraInputAttributes(['class' => 'focus:border-blue-500 rounded-xl']),

                            DatePicker::make('received_date')
                                ->label('Tanggal Terima')
                                ->default(now())
                                ->required()
                                ->prefixIcon('heroicon-m-calendar-days')
                                ->prefixIconColor('primary')
                                ->disabled(fn($record) => $record && $record->purchase->status !== 'Proses Penerimaan'),

                            Select::make('purchase_id')
                                ->label('Referensi PO')
                                ->live()
                                ->prefixIcon('heroicon-m-shopping-bag')
                                ->prefixIconColor('primary')
                                ->relationship(
                                    'purchase',
                                    'po_number', // Kolom pencarian utama tetap po_number
                                    fn($query, $record) => $query->when(
                                        $record,
                                        fn($q) => $q->where('id', $record->purchase_id)->orWhereIn('status', ['Ordered', 'Proses Penerimaan']),
                                        fn($q) => $q->whereIn('status', ['Ordered', 'Proses Penerimaan'])
                                    )
                                )
                                // TAMBAHKAN LOGIKA INI
                                ->getOptionLabelFromRecordUsing(fn($record) => "{$record->po_number} - {$record->supplier?->name}")
                                ->searchable(['po_number']) // Agar user tetap bisa cari berdasarkan nomor PO
                                ->required()
                                ->preload()
                                ->dehydrated()
                                ->afterStateUpdated(function ($state, $set) {
                                    if (!$state) {
                                        $set('items', []);
                                        return;
                                    }
                                    $poItems = \App\Models\PurchaseItem::where('purchase_id', $state)->get();
                                    $items = $poItems->map(function ($item) {
                                        $alreadyReceived = \App\Models\ReceivingItem::where('purchase_item_id', $item->id)->sum('qty_received');
                                        return [
                                            'purchase_item_id' => $item->id,
                                            'product_id' => $item->product_id,
                                            'qty_order' => $item->quantity,
                                            'qty_received' => max(0, $item->quantity - $alreadyReceived),
                                            'qty_rejected' => 0,
                                            'reject_reason' => null,
                                        ];
                                    })->toArray();
                                    $set('items', $items);
                                })
                                ->disabled(fn($record) => $record !== null),

                            Select::make('warehouse_id')
                                ->label('Gudang Penerima')
                                ->relationship('warehouse', 'name')
                                ->prefixIcon('heroicon-m-home-modern')
                                ->prefixIconColor('primary')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->disabled(fn($record) => $record && $record->purchase->status !== 'Proses Penerimaan'),

                            TagsInput::make('delivery_note_number')
                                ->label('No. Surat Jalan Vendor')
                                ->placeholder('Ketik nomor lalu Enter')
                                ->separator(',')
                                ->columnSpanFull()
                                // Warna Tag Biru via CSS Class
                                ->extraAttributes(['class' => 'text-blue-600'])
                                ->disabled(fn($record) => $record && $record->purchase->status !== 'Proses Penerimaan'),
                        ]),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                // --- SECTION 2: ADMIN ---
                Section::make('Data Administrasi')
                    ->description('Petugas penerima dan catatan internal.')
                    ->icon('heroicon-s-user-circle')
                    ->iconColor('primary')
                    ->collapsible() // Bisa di-expand/collapse agar rapi
                    ->schema([
                        Grid::make(3)->schema([ // Gunakan Grid 3 agar catatan lebih luas
                            Select::make('received_by')
                                ->label('Diterima Oleh')
                                ->relationship('user', 'name')
                                ->default(auth()->id())
                                ->disabled()
                                ->dehydrated()
                                ->prefixIcon('heroicon-m-user')
                                ->prefixIconColor('primary')
                                ->columnSpan(1),

                            Textarea::make('notes')
                                ->label('Catatan Internal')
                                ->rows(2)
                                ->placeholder('Tambahkan keterangan jika ada barang rusak atau kurang...')
                                ->columnSpan(2)
                                ->disabled(fn($record) => $record && $record->purchase->status !== 'Proses Penerimaan'),
                        ]),
                    ])
                    ->columnSpanFull(),


                Section::make('Rincian Barang yang Datang')
                    ->description('Daftar barang akan muncul otomatis setelah Referensi PO dipilih.')
                    ->icon('heroicon-o-shopping-cart')
                    ->columnSpanFull()
                    ->schema([
                        // INI BAGIAN VIEWFIELD LENGKAP
                        ViewField::make('items')
                            ->view('filament.forms.components.receiving-item-table')
                            ->columnSpanFull()
                            ->dehydrated() // WAJIB: Agar data dikirim saat tombol simpan ditekan
                            ->reactive()   // Agar perubahan di view langsung terbaca oleh Livewire
                            ->afterStateHydrated(function ($state, $set, $record) {
                                // Logic penarikan data lama saat Edit (sudah benar di kode sebelumnya)
                                if ($record && $record->items) {
                                    $items = $record->items->mapWithKeys(function ($item) {
                                        $uuid = (string) \Illuminate\Support\Str::uuid();
                                        return [$uuid => [
                                            'id' => $item->id,
                                            'purchase_item_id' => $item->purchase_item_id,
                                            'product_id' => $item->product_id,
                                            'product_name' => $item->purchaseItem->product?->name,
                                            'qty_order' => $item->purchaseItem->quantity,
                                            'qty_received' => $item->qty_received,
                                            'qty_rejected' => $item->qty_rejected,
                                            'reject_reason' => $item->reject_reason,
                                        ]];
                                    })->toArray();
                                    $set('items', $items);
                                }
                            }),
                    ]),

            ]);
    }
}
