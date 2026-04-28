<?php

namespace App\Filament\Resources\SalesDetailReports\Tables;

use App\Exports\SalesDetailExport;
use App\Models\SalesDetailReport;
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

class SalesDetailReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')->sortable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('No Penjualan')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('Nama Perusahaan')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('Cabang')
                    ->placeholder('N/A')
                    ->searchable(),
                TextColumn::make('Nama Barang')
                    ->searchable(),
                TextColumn::make('satuan')
                    ->label('Satuan'),
                TextColumn::make('qty')
                    ->label('Qty')
                    ->numeric(),
                TextColumn::make('Harga Jual')
                    ->money('IDR'),
                TextColumn::make('Subtotal Order')
                    ->money('IDR')
                    ->summarize(Sum::make()->label('Total Omzet')),
            ])
            ->filters([
                Filter::make('tanggal')
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

                SelectFilter::make('Nama Perusahaan')
                    ->label('Customer')
                    ->options(fn() => SalesDetailReport::pluck('Nama Perusahaan', 'Nama Perusahaan')->unique()->toArray())
                    ->searchable(),

                SelectFilter::make('Cabang')
                    ->label('Cabang')
                    ->options(
                        fn() => SalesDetailReport::whereNotNull('Cabang')
                            ->pluck('Cabang', 'Cabang')
                            ->unique()
                            ->toArray()
                    )
                    ->searchable()
                    ->placeholder('Semua Cabang'),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->headerActions([
                Action::make('export_excel')
                    ->label('Export Excel')
                    ->color('success')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->action(function ($livewire) {
                        $filterData = $livewire->tableFilters['tanggal'] ?? [];
                        $dari = $filterData['dari'] ?? null;
                        $sampai = $filterData['sampai'] ?? null;
                        $customer = $livewire->tableFilters['Nama Perusahaan'] ?? null;
                        $cabang = $livewire->tableFilters['Cabang'] ?? null;

                        if (!$dari && !$sampai && !$customer && !$cabang) {
                            Notification::make()
                                ->title('Gagal Export')
                                ->body('Wajib pilih semua filter sebelum download.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $data = $livewire->getFilteredTableQuery()->get();
                        $periode = "Periode: " . \Carbon\Carbon::parse($dari)->format('d/m/Y') . " s/d " . \Carbon\Carbon::parse($sampai)->format('d/m/Y');

                        return Excel::download(new SalesDetailExport($data, $periode), 'Laporan_Penjualan_Detail_' . date('Ymd') . '.xlsx');
                    })
            ]);
    }
}
