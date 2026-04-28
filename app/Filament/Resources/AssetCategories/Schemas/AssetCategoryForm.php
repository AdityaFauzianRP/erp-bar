<?php

namespace App\Filament\Resources\AssetCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssetCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kategori')
                    ->description('Kelola master data kategori untuk pengelompokan aset.')
                    ->icon('heroicon-o-tag') // Tambahkan ikon biar cantik
                    ->schema([
                        Grid::make(1) // Memastikan tampilan rapi
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->placeholder('Contoh: Elektronik, Furniture, atau Kendaraan')
                                    ->required()
                                    ->unique(ignoreRecord: true) // Mencegah duplikasi nama kategori
                                    ->maxLength(255)
                                    ->autofocus() // Kursor langsung aktif di sini saat buka modal/page
                                    ->validationAttribute('Nama Kategori'),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(), // Membuat tampilan lebih ringkas
            ]);
    }
}
