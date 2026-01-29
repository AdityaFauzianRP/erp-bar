<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Support\Enums\FontWeight;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // KODE SUPPLIER
                TextColumn::make('code')
                    ->label('Kode')
                    ->fontFamily('mono')
                    ->sortable()
                    ->searchable()
                    ->color('gray'),

                // NAMA SUPPLIER & PIC
                TextColumn::make('name')
                    ->label('Supplier')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => "PIC: " . ($record->pic ?? '-')),

                // KONTAK (Bisa diklik untuk telpon/WA)
                TextColumn::make('phone')
                    ->label('Kontak')
                    ->icon('heroicon-m-phone')
                    ->copyable()
                    ->searchable(),

                // INFORMASI BANK
                TextColumn::make('bank_name')
                    ->label('Rekening Bank')
                    ->description(fn ($record) => $record->bank_account_number ?? 'Belum diatur')
                    ->placeholder('-'),

                // STATUS AKTIF
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->alignCenter(),

                // WAKTU TERDAFTAR
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->trueLabel('Hanya Supplier Aktif')
                    ->falseLabel('Hanya Supplier Non-Aktif'),
            ])
            ->actions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-truck')
            ->emptyStateHeading('Belum ada data supplier')
            ->emptyStateDescription('Mulai tambahkan supplier untuk mengelola katalog produk dan pembelian.');
    }
}