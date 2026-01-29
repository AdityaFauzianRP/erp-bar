<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Master Produk & Harga')
                ->description('Kelola data produk dan margin keuntungan default.')
                ->schema([
                    TextInput::make('code')
                        ->label('Kode Produk')
                        ->disabled()
                        ->placeholder('PRD-YYYYMM-XXXX (Otomatis)')
                        ->dehydrated(false),

                    TextInput::make('name')
                        ->label('Nama Produk')
                        ->required(),

                    TextInput::make('hpp')
                        ->label('Harga Pokok (HPP)')
                        ->numeric()
                        ->prefix('IDR')
                        ->required(),

                    TextInput::make('harga_jual_default')
                        ->label('Harga Jual Default')
                        ->numeric()
                        ->prefix('IDR')
                        ->required(),

                    Select::make('unit_id')
                        ->label('Satuan')
                        ->relationship('unit', 'short_name') // Menampilkan singkatan (Kg/Pcs)
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(1),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }
}
