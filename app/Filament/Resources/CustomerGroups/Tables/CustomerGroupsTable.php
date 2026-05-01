<?php

namespace App\Filament\Resources\CustomerGroups\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomerGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Group')
                    ->searchable(),
                TextColumn::make('price_package')
                    ->label('Paket Harga')
                    ->money('IDR')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])
            ->actions([
                EditAction::make(),
                // DeleteAction::make(),
            ]);
    }
}
