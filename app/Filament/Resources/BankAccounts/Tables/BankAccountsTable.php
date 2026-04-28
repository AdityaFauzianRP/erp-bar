<?php

namespace App\Filament\Resources\BankAccounts\Tables;

use App\Models\BankAccount;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BankAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bank_name')
                    ->label('Bank')
                    ->searchable()
                    ->sortable()
                    ->description(fn(BankAccount $record): string => "Kode: {$record->bank_code}"),

                TextColumn::make('account_number')
                    ->label('No. Rekening')
                    ->copyable() // User bisa klik untuk copy nomor rek
                    ->copyMessage('Nomor rekening berhasil disalin')
                    ->searchable(),

                TextColumn::make('account_holder')
                    ->label('Atas Nama')
                    ->searchable(),

                // TextColumn::make('initial_balance')
                //     ->label('Saldo Awal')
                //     ->money('IDR') // Format Rupiah
                //     ->sortable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                EditAction::make(),
                // DeleteAction::e(),
            ]);
            
    }
}
