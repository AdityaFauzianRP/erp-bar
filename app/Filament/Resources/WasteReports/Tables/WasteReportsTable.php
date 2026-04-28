<?php

namespace App\Filament\Resources\WasteReports\Tables;

use App\Exports\WasteReportExport;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class WasteReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')->sortable(),
                TextColumn::make('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('Kategori')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Penjualan' => 'warning',
                        'Pembelian' => 'info',
                        'Gudang' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('Produk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('Qty Rusak')
                    ->numeric(decimalPlaces: 2)
                    ->label('Qty Rusak'),
                TextColumn::make('Satuan'),
                TextColumn::make('Harga Modal')
                    ->money('IDR'),
                TextColumn::make('Total')
                    ->money('IDR')
                    ->summarize(Sum::make()->label('Total Kerugian')),
            ])
            ->filters([
                // Filter Rentang Tanggal (Wajib)
                Filter::make('Tanggal')
                    ->form([
                        DatePicker::make('dari')->label('Dari Tanggal')->required(),
                        DatePicker::make('sampai')->label('Sampai Tanggal')->required(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['dari'] && $data['sampai'],
                            fn(Builder $query) => $query->betweenDate($data['dari'], $data['sampai'])
                        );
                    })->columnSpan(2)->columns(2),

                SelectFilter::make('Kategori')
                    ->options([
                        'Penjualan' => 'Penjualan',
                        'Pembelian' => 'Pembelian',
                        'Gudang' => 'Gudang',
                    ]),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->headerActions([
                Action::make('export_excel')
                    ->label('Export Excel')
                    ->color('success')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->action(function ($livewire) {
                        $filterData = $livewire->tableFilters['Tanggal'] ?? [];
                        $dari = $filterData['dari'] ?? null;
                        $sampai = $filterData['sampai'] ?? null;

                        $filters = $livewire->tableFilters;
                        $kategori = $filters['Kategori'] ?? null;

                        if (!$dari && !$sampai && !$kategori) {
                            Notification::make()
                                ->title('Gagal Export')
                                ->body('Wajib pilih rentang tanggal dan kategori sebelum download.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $data = $livewire->getFilteredTableQuery()->get();
                        $periode = "Periode: " . \Carbon\Carbon::parse($dari)->format('d/m/Y') . " s/d " . \Carbon\Carbon::parse($sampai)->format('d/m/Y');

                        return Excel::download(new WasteReportExport($data, $periode), 'Laporan_Qty_Rusak_' . date('Ymd') . '.xlsx');
                    })
            ]);
    }
}
