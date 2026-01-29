<?php

namespace App\Filament\Resources\ExpenseCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Informasi Kategori')
                    ->description('Kelola pengelompokan biaya untuk laporan keuangan yang rapi.')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Kategori')
                            ->placeholder('Otomatis (EXP-xxx)')
                            ->disabled()
                            ->dehydrated(false) // Kode dibuat otomatis di Model
                            ->prefixIcon('heroicon-m-qr-code'),

                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->placeholder('Contoh: Gaji Karyawan, Listrik, Marketing')
                            ->prefixIcon('heroicon-m-tag'),

                        Textarea::make('description')
                            ->label('Keterangan Kategori')
                            ->placeholder('Jelaskan rincian pengeluaran untuk kategori ini...')
                            ->rows(3),

                        Toggle::make('is_active')
                            ->label('Status Kategori Aktif')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
