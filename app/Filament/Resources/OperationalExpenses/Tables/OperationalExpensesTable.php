<?php

namespace App\Filament\Resources\OperationalExpenses\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class OperationalExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('number') // Menambahkan kolom nomor dokumen jika ada
                    ->label('No. Pengeluaran')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('item_summary') // Beri nama dummy apa saja
                    ->label('Item List')
                    ->getStateUsing(function ($record) {
                        // Ambil semua item dan format menjadi list di sini
                        return $record->items->map(function ($item) {
                            $productName = $item->product->name ?? 'N/A';
                            $unit = $item->product->unit_name ?? '';
                            return "{$productName} ({$item->qty} {$unit})";
                        });
                    })
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->color('gray')
                    ->size('sm'),

                TextColumn::make('approver.name')
                    ->label('Penanggung Jawab')
                    ->searchable(),

                TextColumn::make('notes')
                    ->label('Keterangan')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('total_amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->label('Total')->money('IDR')),

            ])
            ->filters([

                // Filter Rentang Tanggal
                Filter::make('date')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['until'])->toFormattedDateString();
                        }
                        return $indicators;
                    })
                    ->columns(2)
                    ->columnSpan(2),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->deferFilters()
            ->recordActions([
                EditAction::make(),
                // Delete 
                DeleteAction::make(),
            ])
            ->defaultSort('date', 'desc');
    }
}
