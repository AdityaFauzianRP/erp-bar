<?php

namespace App\Filament\Resources\Suppliers\RelationManagers;

use Filament\Actions\AttachAction as ActionsAttachAction;
use Filament\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Actions\DetachAction as ActionsDetachAction;
use Filament\Actions\DetachBulkAction as ActionsDetachBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
// Import Action Khusus v4
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DetachBulkAction;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $title = 'Katalog Barang Supplier';

    /**
     * Filament v4 Schema Pattern
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku_supplier')
                    ->label('SKU Supplier')
                    ->placeholder('Kode unik barang di supplier ini'),

                // Select::make('branch_id')
                //     ->label('Berlaku di Cabang')
                //     ->options(
                //         fn($livewire) =>
                //         $livewire->getOwnerRecord()->branches->pluck('name', 'id')
                //     )
                //     ->native(false)
                //     ->required(),

                TextInput::make('harga_beli_khusus')
                    ->label('Harga Kontrak Beli')
                    ->numeric()
                    ->prefix('IDR')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode Master')
                    ->fontFamily('mono')
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->description(fn($record) => "SKU Supplier: " . ($record->pivot->sku_supplier ?? '-')),

                // Tables\Columns\TextColumn::make('pivot.branch_id')
                //     ->label('Berlaku di Cabang')
                //     ->formatStateUsing(fn($state) => \App\Models\Branch::find($state)?->name ?? '-')
                //     ->badge()
                //     ->color('warning'),

                Tables\Columns\TextColumn::make('pivot.harga_beli_khusus')
                    ->label('Harga Kontrak')
                    ->money('idr')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('hpp')
                    ->label('HPP Master')
                    ->money('idr')
                    ->color('gray'),
            ])
            ->headerActions([
                ActionsAttachAction::make()
                    ->label('Tambah ke Katalog')
                    ->modalHeading('Pilih Produk Master')
                    ->form(fn(ActionsAttachAction $action) => [
                        $action->getRecordSelect()
                            ->label('Produk Master')
                            ->preload() // Memuat data di awal agar langsung muncul saat diklik
                            ->live()
                            ->afterStateUpdated(function ($state, set $set) { // Pastikan import Set ada
                                if (! $state) {
                                    $set('sku_supplier', null);
                                    $set('harga_beli_khusus', null);
                                    return;
                                }

                                $product = \App\Models\Product::find($state);

                                if ($product) {
                                    // Mengisi SKU Supplier otomatis dengan SKU Master produk
                                    $set('sku_supplier', $product->sku);
                                    // Mengisi Harga Beli otomatis dengan HPP master
                                    $set('harga_beli_khusus', $product->hpp);
                                }
                            }),

                        TextInput::make('sku_supplier')
                            ->label('Kode Barang Supplier')
                            ->placeholder('Otomatis terisi kode master...')
                            ->prefix('SKU')
                            // Tambahkan live() jika ingin field ini memantau perubahan manual
                            ->live(onBlur: true),

                        TextInput::make('harga_beli_khusus')
                            ->label('Harga Beli Khusus Supplier Ini')
                            ->numeric()
                            ->prefix('IDR')
                            ->required(),

                        Hidden::make('branch_id')
                            ->default(1) // Otomatis terpilih ID 1
                    ]),
            ])
            ->actions([
                ActionsEditAction::make()
                    ->modalHeading('Edit Detail Katalog'),

                ActionsDetachAction::make()
                    ->label('Hapus Hubungan'),
            ])
            ->bulkActions([
                ActionsBulkActionGroup::make([
                    ActionsDetachBulkAction::make(),
                ]),
            ]);
    }
}
