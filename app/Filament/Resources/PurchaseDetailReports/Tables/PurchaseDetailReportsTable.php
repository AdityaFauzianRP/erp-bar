<?php

namespace App\Filament\Resources\PurchaseDetailReports\Tables;

use App\Models\PurchaseDetailReport;
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

class PurchaseDetailReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')->label('#')->sortable(),
                TextColumn::make('Tanggal Pembelian')->date('d/m/Y')->sortable(),
                TextColumn::make('No Pembelian')->searchable()->weight('bold'),
                TextColumn::make('Supplier')->searchable()->sortable(),
                TextColumn::make('nama_barang')->label('Nama Barang')->searchable(),
                TextColumn::make('Harga Beli')->money('IDR'),
                TextColumn::make('qty')->numeric(),
                TextColumn::make('satuan'),
                TextColumn::make('Subtotal Order')->money('IDR')->summarize(Sum::make()->label('Total')),

                //
            ])
            ->filters([
                Filter::make('Tanggal Pembelian')
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

                SelectFilter::make('Supplier')
                    ->options(fn() => PurchaseDetailReport::pluck('Supplier', 'Supplier')->unique()->toArray())
                    ->searchable(),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->headerActions([
                Action::make('export_excel')
                    ->label('Export Excel')
                    ->color('success')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->action(function ($livewire) {
                        $filterData = $livewire->tableFilters['Tanggal Pembelian'] ?? [];
                        $dari = $filterData['dari'] ?? null;
                        $sampai = $filterData['sampai'] ?? null;
                        $Supplier = $livewire->tableFilters['Supplier'] ?? null;

                        // Validasi wajib isi tanggal
                        if (!$dari && !$sampai && !$Supplier) {
                            Notification::make()
                                ->title('Gagal Export')
                                ->body('Wajib pilih semua filter sebelum download.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $data = $livewire->getFilteredTableQuery()->get();
                        $periode = "Periode: " . \Carbon\Carbon::parse($dari)->format('d/m/Y') . " s/d " . \Carbon\Carbon::parse($sampai)->format('d/m/Y');

                        // Segera buat file PurchaseDetailExport mirip SalesReportExport
                        return Excel::download(new \App\Exports\PurchaseDetailExport($data, $periode), 'Laporan_Pembelian_Detail_' . date('Ymd') . '.xlsx');
                    })
            ]);
    }
}
