<?php

namespace App\Filament\Resources\CustomerInduks\Tables;

use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class CustomerInduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono'),

                TextColumn::make('name')
                    ->label('Nama Perusahaan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->alias),

                TextColumn::make('email')
                    ->label('Kontak')
                    ->description(fn($record) => $record->phone),

            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Status Aktif'),
                Tables\Filters\TernaryFilter::make('use_custom_price')->label('Harga Khusus'),
            ])
            ->actions([
                ActionsEditAction::make(),
                ActionsDeleteAction::make(),
            ]);
    }
}
