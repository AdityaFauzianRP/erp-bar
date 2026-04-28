<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // KODE PRODUK (Badge Style)
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable() // Bisa diklik untuk copy
                    ->fontFamily('mono')
                    ->weight(FontWeight::Bold)
                    ->color('gray'),

                // NAMA PRODUK
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => "Update terakhir: " . $record->updated_at->format('d/m/Y')),

                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => "Satuan: " . ($record->unit?->name ?? '-')),

                TextColumn::make('unit.short_name')
                    ->label('Unit')
                    ->badge()
                    ->color('danger '),

                // HARGA POKOK (HPP)
                TextColumn::make('hpp')
                    ->label('HPP')
                    ->money('idr')
                    ->sortable()
                    ->color('danger'), // Warna merah untuk pengeluaran

                // HARGA JUAL
                    // TextColumn::make('harga_jual_default')
                    //     ->label('Harga Jual')
                    //     ->money('idr')
                    //     ->sortable()
                    //     ->weight(FontWeight::Bold)
                    //     ->color('success'), // Warna hijau untuk pemasukan

                    // // MONITORING PROFIT (Kalkulasi Otomatis)
                    // TextColumn::make('profit')
                    //     ->label('Margin (Rp)')
                    //     ->state(fn($record) => $record->harga_jual_default - $record->hpp)
                    //     ->money('idr')
                    //     ->color('info'),

                    // TextColumn::make('margin_pct')
                    //     ->label('Margin (%)')
                    //     ->state(function ($record) {
                    //         if ($record->harga_jual_default <= 0) return '0%';
                    //         $margin = (($record->harga_jual_default - $record->hpp) / $record->harga_jual_default) * 100;
                    //         return number_format($margin, 1) . '%';
                    //     })
                    //     ->badge()
                    //     ->color(fn($state) => (float) $state > 20 ? 'success' : 'warning'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Produk'),
            ])
            ->actions([
                EditAction::make(),
            ])
            // ->bulkActions([
            //     BulkActionGroup::make([
            //         DeleteBulkAction::make(),
            //     ]),
            // ])
            ->emptyStateHeading('Belum ada produk')
            ->emptyStateDescription('Mulai tambahkan produk pertama Anda untuk mengelola harga.');
    }
}
