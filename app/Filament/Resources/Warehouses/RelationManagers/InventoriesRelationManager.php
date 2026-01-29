<?php

namespace App\Filament\Resources\Warehouses\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;

class InventoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'inventories';

    protected static ?string $title = 'Daftar Stok Barang';

    public function configure(Schema $form): Schema
    {
        // Form dibuat readonly/view mode karena stok tidak boleh diedit manual
        return $form
            ->schema([
                Forms\Components\TextInput::make('product_id')
                    ->label('ID Produk'),
                Forms\Components\TextInput::make('branch_id')
                    ->label('ID PT/Branch'),
                Forms\Components\TextInput::make('stock')
                    ->label('Jumlah Stok'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                // Menampilkan Nama PT/Branch (Pemilik Barang)
                TextColumn::make('branch.name')
                    ->label('Pemilik (PT)')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                // Menampilkan Detail Produk
                TextColumn::make('product.code')
                    ->label('Kode SKU')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    /** * Menggabungkan Nama Produk dengan Nama Unit dari relasi
                     * Kita gunakan $record untuk mengakses data unit melalui product
                     */
                    ->formatStateUsing(function ($state, $record) {
                        $unit = $record->product?->unit?->name ?? '-';
                        return "{$state} - {$unit}";
                    }),

                // Menampilkan Angka Stok
                TextColumn::make('stock')
                    ->label('Stok Fisik')
                    ->numeric(2)
                    ->alignRight()
                    ->color(fn($record) => $record->stock <= $record->min_stock ? 'danger' : 'success')
                    ->summarize(Sum::make()->label('Total Item')),

                // Menampilkan Lokasi Rak di Gudang ini
                // TextColumn::make('bin_location')
                //     ->label('Lokasi Rak')
                //     ->badge()
                //     ->color('gray')
                //     ->placeholder('N/A'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->relationship('branch', 'name')
                    ->label('Filter PT/Pemilik'),
            ])
            ->headerActions([
                // Kita kosongkan CreateAction karena stok diisi via Opname/Pembelian
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                // Kita tidak ingin ada hapus massal stok
            ]);
    }
}
