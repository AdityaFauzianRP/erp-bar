<?php

namespace App\Filament\Resources\OperationalReports\Tables;

use App\Exports\OperationalReportExport;
use App\Models\OperationalReport;
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

class OperationalReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')->sortable(),
                TextColumn::make('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('No Operasional')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('Item Pengeluaran')
                    ->wrap() // Karena isinya bisa panjang seperti "Bensin: 1.000, Parang : 10.000"
                    ->searchable(),
                TextColumn::make('Total Pengeluaran')
                    ->label('Total Pengeluaran')
                    ->money('IDR')
                    ->summarize(
                        \Filament\Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Total Biaya')
                            ->using(function ($query) {
                                // Ambil semua records dari query tabel saat ini
                                return $query->get()->sum(function ($record) {
                                    // Ambil nilai string, contoh: "Rp10.010.000"
                                    $value = $record->{'Total Pengeluaran'};

                                    if (blank($value)) return 0;


                                    $cleanNumber = preg_replace('/[^0-9]/', '', $value);

                                    return (float) $cleanNumber;
                                });
                            })
                            ->money('IDR') // Format kembali hasilnya ke mata uang
                    ),
                TextColumn::make('Pemohon')
                    ->placeholder('N/A')
                    ->searchable(),
            ])
            ->filters([
                // Filter Tanggal
                Filter::make('Tanggal')
                    ->form([
                        DatePicker::make('dari')->label('Dari Tanggal'),
                        DatePicker::make('sampai')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['dari'] && $data['sampai'],
                            fn(Builder $query) => $query->betweenDate($data['dari'], $data['sampai'])
                        );
                    })->columnSpan(2)->columns(2),

                // Filter Pemohon
                SelectFilter::make('Pemohon')
                    ->options(fn() => OperationalReport::whereNotNull('Pemohon')->pluck('Pemohon', 'Pemohon')->unique()->toArray())
                    ->searchable()
                    ->preload(),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->headerActions([
                Action::make('export_excel')
                    ->label('Export Excel')
                    ->color('success')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->action(function ($livewire) {
                        $filters = $livewire->tableFilters;

                        $dari = $filters['Tanggal']['dari'] ?? null;
                        $sampai = $filters['Tanggal']['sampai'] ?? null;
                        $pj = $filters['Pemohon']['value'] ?? null;

                        // Validasi: Harus ada salah satu filter yang diisi
                        if (!$dari && !$sampai && !$pj) {
                            Notification::make()
                                ->title('Gagal Export')
                                ->body('Pilih rentang tanggal atau Pemohon terlebih dahulu.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $data = $livewire->getFilteredTableQuery()->get();

                        $tglDari = $dari ? \Carbon\Carbon::parse($dari)->format('d/m/Y') : 'Awal';
                        $tglSampai = $sampai ? \Carbon\Carbon::parse($sampai)->format('d/m/Y') : 'Sekarang';
                        $periode = "Periode: {$tglDari} s/d {$tglSampai}";

                        return Excel::download(new OperationalReportExport($data, $periode), 'Laporan_Operasional_' . date('Ymd') . '.xlsx');
                    })
            ]);
    }
}
