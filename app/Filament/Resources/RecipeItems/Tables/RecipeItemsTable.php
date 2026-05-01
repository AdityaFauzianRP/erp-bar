<?php

namespace App\Filament\Resources\RecipeItems\Tables;


use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;



class RecipeItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recipe.product.name')
                    ->label('Bahan Baku Mentah')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('recipe.name')
                    ->label('Resep (Varian)')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('rawMaterial.name')
                    ->label('Bahan Siap Masak')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Gramasi Bahan Baku')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit.name')
                    ->label('Satuan')
                    ->sortable(),
                TextColumn::make('hpp')
                    ->label('HPP')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('kandungan_nutrisi')
                    ->label('Kandungan Nutrisi')
                    ->searchable(),
            ])
            ->filters([
                //
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
