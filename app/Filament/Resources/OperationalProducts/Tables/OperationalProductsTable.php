<?php

namespace App\Filament\Resources\OperationalProducts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OperationalProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('KODE')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('NAMA BARANG')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name') // Mengambil kolom 'name' dari relasi 'category'
                    ->label('Kategori')
                    ->searchable() // Agar kategori bisa dicari di search bar tabel
                    ->sortable()   // Agar bisa diurutkan berdasarkan abjad kategori
                    ->badge()      // Opsional: Biar tampilannya lebih cantik seperti label/badge
                    ->color('info'),


                TextColumn::make('unit.name')
                    ->label('SATUAN'),

                // IconColumn::make('is_active')
                //     ->label('AKTIF')
                //     ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(\App\Models\ExpenseCategory::pluck('name', 'id')),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }
}
