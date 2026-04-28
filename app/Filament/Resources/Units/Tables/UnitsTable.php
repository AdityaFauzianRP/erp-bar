<?php

namespace App\Filament\Resources\Units\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;

class UnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // NAMA SATUAN (Lengkap)
                TextColumn::make('name')
                    ->label('Nama Satuan')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                // SIMBOL / SINGKATAN (Dengan Badge)
                TextColumn::make('short_name')
                    ->label('Simbol')
                    ->searchable()
                    ->fontFamily('mono')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                // STATISTIK PENGGUNAAN
                TextColumn::make('products_count')
                    ->label('Digunakan di')
                    ->counts('products') // Menghitung relasi hasMany di model Unit
                    ->suffix(' Produk')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                // WAKTU PEMBUATAN
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Gunakan EditAction yang sudah diimport dari Tables
                ActionsEditAction::make()
                    ->label('Lanjutkan Proses') 
                    ->icon('heroicon-m-arrow-right-circle')
                    ->color('primary')
                    ->button(), // PAKAI ->button() agar teks label keluar
            ])
            ->emptyStateHeading('Belum ada satuan')
            ->emptyStateDescription('Tambahkan satuan seperti Kg, Pcs, atau Box untuk produk Anda.')
            ->emptyStateIcon('heroicon-o-scale');
    }
}
