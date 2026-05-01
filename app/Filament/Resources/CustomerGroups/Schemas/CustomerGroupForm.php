<?php

namespace App\Filament\Resources\CustomerGroups\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Group Pelanggan')
                    ->icon('heroicon-m-user-group')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Group')
                                ->required(),
                            TextInput::make('code')
                                ->label('Kode')
                                ->default(function () {
                                    $lastRecord = \App\Models\CustomerGroup::orderBy('id', 'desc')->first();
                                    $lastNumber = $lastRecord ? (int) substr($lastRecord->code, -4) : 0;

                                    return 'CUST-G-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                                })
                                ->readOnly() // Agar tidak diubah manual oleh user
                                ->required(),

                        ]),
                        TextInput::make('price_package')
                            ->label('Paket Harga')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp'),
                        Textarea::make('description')
                            ->label('Keterangan'),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->onColor('success'),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
