<?php

namespace App\Filament\Resources\Recipes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class RecipeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Repeater::make('recipeItems')
                    ->relationship()
                    ->schema([
                        Select::make('raw_material_id')
                            ->relationship('rawMaterial', 'name')
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('quantity')
                            ->label('Gramasi Bahan Baku')
                            ->numeric()
                            ->required()
                            ->columnSpan(1),
                        Select::make('unit_id')
                            ->relationship('unit', 'name')
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('hpp')
                            ->label('HPP')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->columnSpan(2),
                        TextInput::make('kandungan_nutrisi')
                            ->label('Kandungan Nutrisi')
                            ->maxLength(255)
                            ->columnSpan(2),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }
}
