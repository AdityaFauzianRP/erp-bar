<?php

namespace App\Filament\Resources\ReceivingReports\Tables;

use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class ReceivingReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom Utama: Nomor Penerimaan dengan Desain Badge
                TextColumn::make('receive_number')
                    ->label('ID PENERIMAAN')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->fontFamily('mono')
                    ->color('primary')
                    ->copyable()
                    ->description(fn($record) => "Gudang: " . ($record->warehouse?->name ?? 'Pusat')),

                // Kolom PO dengan Icon
                TextColumn::make('purchase.po_number')
                    ->label('REFERENSI PO')
                    ->searchable()
                    ->icon('heroicon-m-document-text')
                    ->iconColor('gray')
                    ->description(fn($record) => $record->purchase?->supplier?->name ?? 'Tanpa Supplier'),

                // Tanggal dengan Badge Indikator
                TextColumn::make('received_date')
                    ->label('TANGGAL TERIMA')
                    ->date('d M Y')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                // Total Item dengan Style Bulat
                TextColumn::make('items_count')
                    ->label('ITEMS')
                    ->counts('items')
                    ->badge()
                    ->color('success')
                    ->alignCenter(),

                // Nama Penerima dengan Inisial/Avatar Circle
                TextColumn::make('user.name')
                    ->label('PETUGAS GUDANG')
                    ->icon('heroicon-m-user-circle')
                    ->sortable(),

                TextColumn::make('purchase.status')
                    ->label('STATUS PO')
                    ->badge()
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'proses penerimaan' => 'info',
                        'menunggu proses pembayaran' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => strtoupper($state))
                    ->description(fn($record) => "Update: " . $record->purchase?->updated_at?->format('d/m/Y')),
            ])
            ->filters([
                // Filter berdasarkan PO
                SelectFilter::make('purchase_id')
                    ->label('Referensi PO')
                    ->relationship('purchase', 'po_number')
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan Gudang
                SelectFilter::make('warehouse_id')
                    ->label('Gudang')
                    ->relationship('warehouse', 'name')
                    ->preload(),

                // Filter Rentang Tanggal Terima
                Filter::make('received_date')
                    ->form([
                        DatePicker::make('from')->label('Terima Dari'),
                        DatePicker::make('until')->label('Terima Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('received_date', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('received_date', '<=', $date));
                    })
                    ->columns(2)
                    ->columnSpan(2),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->deferFilters()
            ->actions([
                ActionsEditAction::make()
                    ->label('Lanjutkan Proses')
                    ->icon('heroicon-m-arrow-right-circle')
                    ->color('info')
                    ->button()
                    ->size('sm')
                    ->visible(function ($record) {
                        return strtolower($record->purchase?->status ?? '') === 'proses penerimaan';
                    }),
            ])
            ->defaultSort('received_date', 'desc');
    }
}