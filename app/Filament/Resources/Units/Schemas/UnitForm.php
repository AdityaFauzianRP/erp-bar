<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Master Satuan')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Satuan')
                            ->required()
                            ->placeholder('Contoh: Kilogram'),
                        TextInput::make('short_name')
                            ->label('Simbol/Singkatan')
                            ->required()
                            ->placeholder('Contoh: Kg'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
