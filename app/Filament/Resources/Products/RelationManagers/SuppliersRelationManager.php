<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class SuppliersRelationManager extends RelationManager
{
    protected static string $relationship = 'suppliers';

    protected static ?string $title = 'Perbandingan Harga Supplier';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            // PERBAIKAN: Gunakan nama kolom asli (harga_beli_khusus), bukan pivot.harga_beli_khusus
            ->defaultSort('harga_beli_khusus', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Supplier')
                    ->weight(FontWeight::Bold),

                // Di kolom tabel, kita tetap gunakan pivot. agar Filament tahu ini data dari tabel tengah
                Tables\Columns\TextColumn::make('pivot.harga_beli_khusus')
                    ->label('Harga Kontrak')
                    ->money('idr')
                    ->sortable() // Ini akan otomatis mencari kolom 'harga_beli_khusus'
                    ->color(
                        fn($state, $table) =>
                        $state === $table->getRecords()->min('pivot.harga_beli_khusus')
                            ? 'success'
                            : 'gray'
                    ),

                Tables\Columns\TextColumn::make('pivot.sku_supplier')
                    ->label('SKU di Supplier'),

                // Indikator "Termurah" menggunakan Badge
                Tables\Columns\TextColumn::make('status_harga')
                    ->label('Rekomendasi')
                    ->getStateUsing(
                        fn($record, $table) =>
                        $record->pivot->harga_beli_khusus === $table->getRecords()->min('pivot.harga_beli_khusus')
                            ? 'TERMURAH'
                            : ''
                    )
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                //
            ]);
    }
}
