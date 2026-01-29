<?php

namespace App\Filament\Resources\CustomerInduks\RelationManagers;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Grid as LayoutGrid;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'customerPrices';

    protected static ?string $title = 'Konfigurasi Harga Produk Spesial';

    public function form(Schema $schema): Schema
    {
        // Form standar untuk Edit satuan
        return $schema
            ->schema([
                ComponentsGrid::make(2)->schema([
                    Select::make('product_id')
                        ->label('Produk')
                        ->relationship('product', 'name')
                        ->disabled() // Dimatikan saat edit agar tidak mengubah relasi produk
                        ->required(),

                    TextInput::make('harga_khusus')
                        ->label('Harga Kontrak')
                        ->numeric()
                        ->prefix('IDR')
                        ->required(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_id')
            ->columns([
                TextColumn::make('product.code')
                    ->label('Kode Produk')
                    ->fontFamily('mono'),

                TextColumn::make('product.name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('product.harga_jual_default')
                    ->label('Harga Umum')
                    ->money('idr')
                    ->color('gray'),

                // REFERENSI MODAL (HPP)
                TextColumn::make('product.hpp')
                    ->label('Harga HPP')
                    ->money('idr')
                    ->color('gray')
                    ->weight(FontWeight::Medium),

                TextColumn::make('harga_khusus')
                    ->label('Harga Kontrak')
                    ->money('idr')
                    ->badge()
                    ->color('success'),

                // Harga Khusus (Harga Kontrak)
                TextColumn::make('harga_khusus')
                    ->label('Harga Kontrak')
                    ->money('idr')
                    ->badge()
                    ->color('success')
                    ->weight(FontWeight::Bold),

                // Margin Nominal (Harga Khusus - HPP)
                TextColumn::make('margin_rp')
                    ->label('Margin (Rp)')
                    ->state(function ($record) {
                        return $record->harga_khusus - $record->product->hpp;
                    })
                    ->money('idr')
                    ->color(fn($state) => $state > 0 ? 'success' : 'danger')
                    ->weight(FontWeight::SemiBold),

                // Persentase Profit (%)
                TextColumn::make('margin_pct')
                    ->label('Profit (%)')
                    ->state(function ($record) {
                        if ($record->harga_khusus <= 0) return '0%';
                        $profit = (($record->harga_khusus - $record->product->hpp) / $record->harga_khusus) * 100;
                        return number_format($profit, 1) . '%';
                    })
                    ->badge()
                    // Warna dinamis: Merah jika profit < 5%, Kuning jika < 15%, Hijau jika bagus
                    ->color(function ($state) {
                        $pct = (float) $state;
                        if ($pct <= 5) return 'danger';
                        if ($pct <= 15) return 'warning';
                        return 'success';
                    }),
            ])
            ->headerActions([
                // MENGGUNAKAN REPEATER UNTUK MULTI-INSERT (BULK)
                CreateAction::make()
                    ->label('Tambah Banyak Produk')
                    ->modalHeading('Input Daftar Harga Khusus')
                    ->modalWidth('4xl') // Buat modal lebih lebar
                    ->using(function (array $data, string $model): void {
                        // Logika kustom untuk menyimpan data dari repeater
                        foreach ($data['items'] as $item) {
                            $this->getOwnerRecord()->customerPrices()->create([
                                'product_id' => $item['product_id'],
                                'harga_khusus' => $item['harga_khusus'],
                            ]);
                        }
                    })
                    ->form([
                        Repeater::make('items')
                            ->label('Daftar Produk')
                            ->schema([
                                ComponentsGrid::make(2)->schema([
                                    Select::make('product_id')
                                        ->label('Pilih Produk')
                                        ->options(Product::where('is_active', true)->pluck('name', 'id'))
                                        ->searchable()
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, $set) {
                                            $product = Product::find($state);
                                            if ($product) {
                                                $set('harga_khusus', $product->harga_jual_default);
                                            }
                                        }),

                                    TextInput::make('harga_khusus')
                                        ->label('Harga Kontrak')
                                        ->numeric()
                                        ->prefix('IDR')
                                        ->required(),
                                ]),
                            ])
                            ->columns(1)
                            ->createItemButtonLabel('Tambah Baris Produk')
                            ->addActionLabel('Tambah Produk Lain')
                            ->reorderable(false),
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
