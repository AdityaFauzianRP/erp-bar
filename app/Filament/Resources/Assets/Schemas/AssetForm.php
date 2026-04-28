<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->icon('heroicon-o-cube')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('asset_category_id')
                                ->label('Kategori Aset')
                                ->relationship('category', 'name') // Mengambil data dari tabel categories
                                ->searchable()
                                ->preload()
                                ->required()
                                ->native(false),

                            TextInput::make('nama_aset')
                                ->label('Nama Barang')
                                ->placeholder('Contoh: Laptop Dell XPS 15')
                                ->required(),

                            DatePicker::make('tanggal_beli')
                                ->label('Tanggal Perolehan')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->required(),

                            Select::make('status')
                                ->label('Kondisi Aset')
                                ->options([
                                    'active' => 'Aktif (Digunakan)',
                                    'perlu_perawatan' => 'Perlu Perawatan',
                                    'tidak_layak' => 'Tidak Layak Pakai',
                                ])
                                ->default('active')
                                ->required()
                                ->native(false),
                        ])
                        ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Detail Biaya')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('jumlah')
                                ->label('Qty')
                                ->numeric()
                                ->default(1)
                                ->live(onBlur: true) // Memicu hitung ulang saat pindah field
                                ->afterStateUpdated(fn(Get $get, Set $set) => self::updateTotal($get, $set))
                                ->required(),

                            TextInput::make('harga_satuan')
                                ->label('Harga Satuan')
                                ->numeric()
                                ->prefix('Rp')
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Get $get, Set $set) => self::updateTotal($get, $set))
                                ->required(),

                            TextInput::make('total_harga')
                                ->label('Total Nilai Aset')
                                ->prefix('Rp')
                                ->readonly() // Agar user tidak edit manual
                                ->extraInputAttributes(['class' => 'font-bold text-primary-600'])
                                ->numeric(),
                        ])
                        ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Informasi Tambahan')
                    ->schema([
                        Textarea::make('catatan')
                            ->placeholder('Catatan mengenai kondisi fisik atau nomor seri...')
                            ->rows(3),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function updateTotal(Get $get, Set $set): void
    {
        $jumlah = (float) ($get('jumlah') ?? 0);
        $harga = (float) ($get('harga_satuan') ?? 0);
        
        $set('total_harga', $jumlah * $harga);
    }
}
