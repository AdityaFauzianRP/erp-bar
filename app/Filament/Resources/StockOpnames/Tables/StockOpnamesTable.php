<?php

namespace App\Filament\Resources\StockOpnames\Tables;

use App\Filament\Resources\StockOpnames\Pages\EditStockOpname;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class StockOpnamesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('opname_number')
                    ->label('Nomor Dokumen')
                    ->searchable()
                    ->sortable()
                    ->copyable() // Biar gampang copy nomor dokumen
                    ->fontFamily('mono'),

                TextColumn::make('warehouse.name')
                    ->label('Gudang')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'draft' => 'heroicon-m-pencil-square',
                        'completed' => 'heroicon-m-check-circle',
                        'cancelled' => 'heroicon-m-x-circle',
                    }),

                TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Waktu Entry')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->label('Filter Gudang'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                ViewAction::make(),

                // Tombol Eksekusi Stok
                Action::make('complete')
                    ->label('Selesaikan Opname')
                    ->color('success')
                    ->icon('heroicon-m-check-circle')
                    ->requiresConfirmation() // Menghindari klik tidak sengaja
                    ->modalHeading('Konfirmasi Selesai')
                    ->modalDescription('Setelah diselesaikan, stok di gudang akan diperbarui dan dokumen ini tidak bisa diedit lagi. Lanjutkan?')
                    ->visible(fn($record) => $record->status === 'draft')
                    ->action(function ($record) {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record) {
                            foreach ($record->items as $item) {
                                // Update atau Buat data di tabel Inventory
                                \App\Models\Inventory::updateOrCreate(
                                    [
                                        'warehouse_id' => $record->warehouse_id,
                                        'product_id' => $item->product_id,
                                        'branch_id' => $item->branch_id,
                                    ],
                                    [
                                        'stock' => $item->physical_stock, // Ganti saldo lama dengan hasil hitung fisik
                                    ]
                                );
                            }

                            // Ubah status dokumen agar tombol Edit & Selesaikan hilang
                            $record->update(['status' => 'completed']);
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Stok Berhasil Diperbarui')
                            ->success()
                            ->send();
                    }),

                EditAction::make()
                    ->visible(fn($record) => $record->status === 'draft'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])

            ->recordUrl(function ($record) {
                // Jika status completed, arahkan ke View, jangan ke Edit
                if ($record->status === 'completed') {
                    return null; // ViewAction sudah otomatis diterapkan
                }

                // Jika masih draft, boleh ke halaman Edit
                return EditStockOpname::getUrl([$record->id]);

                // Atau jika ingin baris sama sekali TIDAK bisa diklik: return null;
            })
            ->defaultSort('created_at', 'desc'); // Tampilkan yang terbaru di paling atas
    }
}
