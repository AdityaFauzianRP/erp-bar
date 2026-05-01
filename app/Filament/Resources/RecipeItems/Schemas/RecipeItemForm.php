<?php

namespace App\Filament\Resources\RecipeItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RecipeItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('recipe_id')
                    ->relationship('recipe', 'name')
                    ->label('Resep (Varian)')
                    ->required(),
                Select::make('raw_material_id')
                    ->relationship('rawMaterial', 'name')
                    ->label('Bahan Siap Masak')
                    ->required(),
                TextInput::make('quantity')
                    ->label('Gramasi Bahan Baku')
                    ->numeric()
                    ->required(),
                Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->label('Satuan')
                    ->required(),
                TextInput::make('hpp')
                    ->label('HPP')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                TextInput::make('kandungan_nutrisi')
                    ->label('Kandungan Nutrisi')
                    ->maxLength(255),
            ]);
    }
}
