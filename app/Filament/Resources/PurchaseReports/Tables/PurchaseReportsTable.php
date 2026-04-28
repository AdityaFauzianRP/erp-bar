<?php

namespace App\Filament\Resources\PurchaseReports\Tables;

use App\Exports\PurchaseReportExport;
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

class PurchaseReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')->label('#')->sortable(),
                TextColumn::make('Tanggal Pembelian')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('No Pembelian')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('Supplier')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('Jumlah Order')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('Total Order')
                    ->money('IDR')
                    ->summarize(Sum::make()->label('Total Keseluruhan')),
                TextColumn::make('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => strtoupper($state))
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'paid' => 'success',
                        'unpaid' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                // Filter Rentang Tanggal (Wajib)
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
                    ->options(fn() => \App\Models\PurchaseReport::pluck('Supplier', 'Supplier')->unique()->toArray())
                    ->searchable()
                    ->preload(), // Agar data supplier langsung muncul saat diklik

                SelectFilter::make('Status')
                    ->options([
                        'paid' => 'PAID',
                        'unpaid' => 'UNPAID',
                    ]),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)

            ->headerActions([
                Action::make('export_excel')
                    ->label('Export Excel')
                    ->color('success')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->action(function ($livewire) {
                        // 1. Ambil semua data filter yang sedang aktif
                        $filters = $livewire->tableFilters;

                        $dari = $filters['Tanggal Pembelian']['dari'] ?? null;
                        $sampai = $filters['Tanggal Pembelian']['sampai'] ?? null;
                        $supplier = $filters['Supplier']['value'] ?? null;
                        $status = $filters['Status']['value'] ?? null;

                        // 2. VALIDASI: Cek apakah SEMUA filter kosong?
                        // Jika Tanggal kosong DAN Supplier kosong DAN Status kosong, baru kita cekal.
                        if (!$dari && !$sampai && !$supplier && !$status) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal Export')
                                ->body('Silakan pilih salah satu filter (Tanggal, Supplier, atau Status) sebelum download.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // 3. Eksekusi Export
                        $data = $livewire->getFilteredTableQuery()->get();

                        // Format Periode untuk Judul Excel
                        $tglDari = $dari ? \Carbon\Carbon::parse($dari)->format('d/m/Y') : 'Awal';
                        $tglSampai = $sampai ? \Carbon\Carbon::parse($sampai)->format('d/m/Y') : 'Sekarang';
                        $periode = "Periode: {$tglDari} s/d {$tglSampai}";

                        return Excel::download(
                            new PurchaseReportExport($data, $periode),
                            'Laporan_Pembelian_' . date('Ymd') . '.xlsx'
                        );
                    })
            ]);
    }
}
