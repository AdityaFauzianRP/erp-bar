<?php

namespace App\Filament\Resources\Warehouses\Schemas;

use App\Models\Warehouse;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WarehouseForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Gudang')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Gudang')
                            ->default(fn() => 'WH-' . strtoupper(str()->random(5))) // Generate kode unik otomatis
                            ->placeholder('Otomatis...')
                            ->disabled() // User tidak bisa ubah manual
                            ->dehydrated() // Tetap dikirim ke database saat simpan
                            ->required()
                            ->unique(Warehouse::class, 'code', ignoreRecord: true),

                        TextInput::make('name')
                            ->label('Nama Gudang')
                            ->required()
                            ->placeholder('Contoh: Gudang Pusat Jakarta'),

                        TextInput::make('location')
                            ->label('Lokasi/Alamat')
                            ->placeholder('Nama gedung atau kota'),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),

                        Textarea::make('description')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
