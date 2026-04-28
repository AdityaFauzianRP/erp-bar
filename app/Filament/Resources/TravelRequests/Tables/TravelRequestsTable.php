<?php

namespace App\Filament\Resources\TravelRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TravelRequestsTable
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

                // Pengajuan atas nama siapa ambil dari create by dan relasi ke tabel user 
                TextColumn::make('creator.name')
                    ->label('PEMOHON')
                    ->searchable(),

                TextColumn::make('total_amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->label('Total')),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge() // Membuat tampilan seperti label/badge
                    ->color(fn(string $state): string => match ($state) {
                        'Draft' => 'gray',      // Warna abu-abu untuk Draft
                        'Approve' => 'success', // Warna hijau untuk Approve
                        'Rejected' => 'danger', // Tambahan: merah jika ada status rejected
                        default => 'info',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Draft' => 'heroicon-m-pencil-square',
                        'Approve' => 'heroicon-m-check-circle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Approve' => 'Approve',
                    ]),

                Filter::make('date')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
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
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['from'])->format('d M Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['until'])->format('d M Y');
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
                DeleteAction::make(),
            ]);
    }
}
