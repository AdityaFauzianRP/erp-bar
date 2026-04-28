<?php

namespace App\Filament\Resources\Assets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // 1. Menampilkan Nama Kategori (bukan ID)
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable(),

                // 2. Nama Aset dengan Deskripsi Catatan (Opsional)
                TextColumn::make('nama_aset')
                    ->label('Nama Aset')
                    ->description(fn($record) => str($record->catatan)->limit(30)) // Munculkan sedikit catatan di bawah nama
                    ->searchable()
                    ->sortable(),

                // 3. Tanggal dengan format Indonesia
                TextColumn::make('tanggal_beli')
                    ->label('Tgl Beli')
                    ->date('d M Y')
                    ->sortable(),

                // 4. Jumlah (Qty)
                TextColumn::make('jumlah')
                    ->label('Qty')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                // 5. Harga Satuan & Total dengan format Rupiah
                TextColumn::make('harga_satuan')
                    ->label('Total Nilai')
                    ->money('idr')
                    ->weight('bold')
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->money('idr')
                    ->weight('bold')
                    ->color('primary')
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->label('Total Nilai Aset')
                            ->money('idr') // Format rupiah juga di bagian summary
                    ),



                // 6. Status dengan Badge Warna
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',           // Hijau
                        'perlu_perawatan' => 'warning',  // Kuning
                        'tidak_layak' => 'danger',       // Merah
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'perlu_perawatan' => 'Perawatan',
                        'tidak_layak' => 'Rusak/Afkir',
                        default => $state,
                    })
                    ->sortable(),

                // 7. Audit Trail (Hidden by default)
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
