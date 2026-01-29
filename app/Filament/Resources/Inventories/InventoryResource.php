<?php

namespace App\Filament\Resources\Inventories;

use App\Filament\Resources\Inventories\Pages\ManageInventories;
use App\Models\Inventory;
use UnitEnum;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Stok Barang';

    protected static ?string $slug = 'stok-barang';

    // Kelompokkan di menu Inventory agar rapi
    protected static string|\UnitEnum|null $navigationGroup =  'Inventory';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name' , 'product')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => $state . ' - ' . ($record->product?->unit?->name ?? ''))
                    ->description(fn($record) => "Code Product: " . ($record->product->code ?? '-')),

                TextColumn::make('warehouse.name')
                    ->label('Gudang')
                    ->sortable(),

                TextColumn::make('branch.name')
                    ->label('Cabang (PT)')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Sisa Stok')
                    ->numeric()
                    ->alignEnd()
                    ->sortable()
                    ->weight('bold')
                    ->color(fn($state) => $state <= 5 ? 'danger' : 'success')
                    ->suffix(fn($record) => " " . ($record->product?->unit?->name ?? '')),

                TextColumn::make('updated_at')
                    ->label('Update Terakhir')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tambahkan filter biar user makin gampang cari barang
                SelectFilter::make('warehouse_id')
                    ->label('Filter Gudang')
                    ->relationship('warehouse', 'name'),

                SelectFilter::make('branch_id')
                    ->label('Filter Cabang')
                    ->relationship('branch', 'name'),
            ])
            ->actions([
                // Kita hanya kasih ViewAction saja, tidak ada Edit/Delete
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    // PENTING: Matikan tombol "Create" di halaman ini
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInventories::route('/'),
        ];
    }
}
