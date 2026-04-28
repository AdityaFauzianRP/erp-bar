<?php

namespace App\Filament\Resources\StockOpnames\Tables;

use App\Filament\Resources\StockOpnames\Pages\EditStockOpname;
use App\Filament\Resources\StockOpnames\StockOpnameResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;

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
                // Filter berdasarkan Gudang
                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->label('Gudang')
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan Status
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Dokumen')
                    ->options([
                        'draft' => 'Draft',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                // Filter Rentang Tanggal Opname
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('date', '<=', $date));
                    })
                    ->columns(2)
                    ->columnSpan(2),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->actions([
                // SEKARANG INI ADALAH Tables\Actions\Action
                Action::make('complete')
                    ->label('Selesaikan Opname')
                    ->color('success')
                    ->icon('heroicon-m-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Selesai')
                    ->modalDescription('Setelah diselesaikan, stok di gudang akan diperbarui...')
                    ->visible(function ($record) {
                        return $record->status === 'draft' &&
                            auth()->user()->can('ApproveStockOpname');
                    })
                    // ->visible(fn($record) => $record->status === 'draft')
                    ->action(function ($record) {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record) {
                            foreach ($record->items as $item) {
                                \App\Models\Inventory::updateOrCreate(
                                    [
                                        'warehouse_id' => $record->warehouse_id,
                                        'product_id' => $item->product_id,
                                        'branch_id' => $item->branch_id,
                                    ],
                                    [
                                        'stock' => $item->physical_stock,
                                    ]
                                );
                            }
                            $record->update(['status' => 'completed']);
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Stok Berhasil Diperbarui')
                            ->success()
                            ->send();
                    }),

                EditAction::make('edit')
                    ->label('Lihat Detail'),
            ])


            ->defaultSort('created_at', 'desc');
    }
}
