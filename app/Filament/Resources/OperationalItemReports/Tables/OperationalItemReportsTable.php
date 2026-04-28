<?php

namespace App\Filament\Resources\OperationalItemReports\Tables;

use App\Exports\OperationalItemReportExport;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class OperationalItemReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')->sortable(),
                TextColumn::make('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                // TextColumn::make('Kategori Pengeluaran')
                //     ->placeholder('Tanpa Kategori')
                //     ->searchable()
                //     ->sortable(),
                TextColumn::make('Item')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('Qty')
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('Satuan'),
                TextColumn::make('Subtotal')
                    ->money('IDR')
                    ->summarize(Sum::make()->label('Total')),
            ])
            ->filters([
                Filter::make('Tanggal')
                    ->form([
                        DatePicker::make('dari')->label('Dari Tanggal'),
                        DatePicker::make('sampai')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['dari'] && $data['sampai'],
                            // Menggunakan whereBetween karena model tidak punya scopeBetweenDate
                            fn(Builder $query) => $query->whereBetween('Tanggal', [$data['dari'], $data['sampai']])
                        );
                    })->columnSpan(2)->columns(2),
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

                        // Validasi: Wajib isi tanggal karena ini satu-satunya filter
                        if (!$dari || !$sampai) {
                            Notification::make()
                                ->title('Gagal Export')
                                ->body('Silakan pilih rentang tanggal terlebih dahulu.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $data = $livewire->getFilteredTableQuery()->get();
                        $periode = "Periode: " . \Carbon\Carbon::parse($dari)->format('d/m/Y') . " s/d " . \Carbon\Carbon::parse($sampai)->format('d/m/Y');

                        return Excel::download(new OperationalItemReportExport($data, $periode), 'Laporan_Detail_Item_' . date('Ymd') . '.xlsx');
                    })
            ]);
    }
}
