<?php

namespace App\Filament\Resources\Inventories;

use App\Filament\Resources\Inventories\Pages\ManageInventories;
use App\Models\Inventory;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-archive-box';

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
                TextColumn::make('product.name', 'product')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($state, $record) => $state . ' - ' . ($record->product?->unit?->name ?? ''))
                    ->description(fn($record) => "Code Product: " . ($record->product->code ?? '-')),

                TextColumn::make('warehouse.name')
                    ->label('Gudang')
                    ->sortable(),

                // TextColumn::make('branch.name')
                //     ->label('Cabang (PT)')
                //     ->sortable(),

                TextColumn::make('stock')
                    ->label('Sisa Stok')
                    ->numeric()
                    ->alignEnd()
                    ->sortable()
                    ->weight('bold')
                    ->color(fn($state) => $state <= 5 ? 'danger' : 'success')
                    ->suffix(fn($record) => " " . ($record->product?->unit?->name ?? '')),

                // TextColumn::make('updated_at')
                //     ->label('Update Terakhir')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('warehouse_id')
                    ->label('Filter Gudang')
                    ->placeholder('Semua Gudang')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false), // UI modern tanpa crash
            ])
            // 1. PINDAHKAN KE ATAS (Area Garis Merah)
            ->filtersLayout(FiltersLayout::AboveContent)

            // 2. KUNCI UTAMA: Ini yang bikin filter jalan otomatis (Trigger Instan)
            // Saat diset false, Filament tidak akan memunculkan tombol "Apply"
            ->deferFilters(false)

            ->filtersFormColumns(4)

            ->actions([
                // Kita hanya kasih ViewAction saja, tidak ada Edit/Delete
                // ViewAction::make(),
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
