<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

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
                        ->required()
                        ->maxLength(255)
                        // Mencegah spasi berlebih di depan/belakang agar tidak lolos validasi unique
                        ->dehydrated(fn($state) => trim($state))
                        ->unique(
                            table: 'products', // Nama tabel Anda
                            column: 'name',    // Nama kolom yang dicek
                            ignoreRecord: true
                        )
                        ->validationMessages([
                            'unique' => 'Nama produk ini sudah ada, silakan gunakan nama lain agar tidak redundan.',
                        ]),

                    TextInput::make('hpp')
                        ->label('Harga Pokok (HPP)')
                        ->prefix('IDR')
                        ->required()
                        // 1. Use mask only for the UI (display)
                        ->mask(RawJs::make('$money($input, ",", ".", 0)'))
                        ->stripCharacters(['.', ','])
                        ->numeric()
                        // 2. Ensure data sent to DB is a clean integer/float
                        ->dehydrateStateUsing(fn($state) => (int) str_replace(['.', ','], '', $state))
                        ->extraInputAttributes(['step' => 'any']),

                    // TextInput::make('harga_jual_default')
                    //     ->label('Harga Jual Default')
                    //     ->numeric()
                    //     ->prefix('IDR')
                    //     ->required(),

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
