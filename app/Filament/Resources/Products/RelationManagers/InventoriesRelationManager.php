<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InventoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'inventories';

    protected static ?string $title = 'Posisi Stok di Gudang';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('Gudang')
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Pemilik (PT)')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Jumlah Stok')
                    ->numeric()
                    ->alignEnd()
                    ->sortable()
                    // Menampilkan satuan yang tadi kita bahas
                    ->suffix(fn ($record) => " " . ($record->product?->unit?->name ?? '')),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Biasanya di sini kita matikan CreateAction agar stok 
                // hanya bisa berubah via Opname atau Transaksi
            ])
            ->actions([
                // Kita biarkan kosong atau hanya ViewAction
            ])
            ->bulkActions([]);
    }
}
