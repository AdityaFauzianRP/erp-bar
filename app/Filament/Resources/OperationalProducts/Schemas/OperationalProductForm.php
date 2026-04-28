<?php

namespace App\Filament\Resources\OperationalProducts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OperationalProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Informasi Barang')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Barang')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default('OP-' . date('Ymd-His')), // Auto-generate simple code

                        TextInput::make('name')
                            ->label('Nama Barang')
                            ->required(),


                        Select::make('unit_id')
                            ->label('Satuan')
                            ->options(\App\Models\Unit::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->options(\App\Models\ExpenseCategory::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
