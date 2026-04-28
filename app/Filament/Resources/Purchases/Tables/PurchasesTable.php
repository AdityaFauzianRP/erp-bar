<?php

namespace App\Filament\Resources\Purchases\Tables;

use App\Filament\Exports\PurchaseExporter;
use App\Filament\Resources\Purchases\PurchaseResource;
use App\Models\Transaction;
use App\Models\Purchase;
use Filament\Actions\Action as ActionsAction;
use Filament\Actions\ExportAction as ActionsExportAction;
use Filament\Tables\Actions\Action; // Gunakan Action biasa, bukan EditAction
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PurchasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query, Table $table) {
                // 1. Ambil state dari filter
                $dateState = $table->getFilter('created_at_range')?->getState();
                $supplierState = $table->getFilter('supplier_id')?->getState();
                $statusState = $table->getFilter('status')?->getState();

                // 2. Sembunyikan data jika semua filter kosong (Reset State)
                if (
                    blank($dateState['from'] ?? null) &&
                    blank($dateState['until'] ?? null) &&
                    blank($supplierState['value'] ?? null) &&
                    blank($statusState['value'] ?? null)
                ) {
                    return $query->whereRaw('1 = 0');
                }

                return $query->withSum('items as total_quantity', 'quantity');
            })
            ->columns([
                TextColumn::make('po_number')
                    ->label('No. Purchase Order')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('info')
                    ->icon('heroicon-m-hashtag')
                    ->copyable()
                    ->description(fn($record) => "ID: #TRX-" . str_pad($record->id, 5, '0', STR_PAD_LEFT)),

                TextColumn::make('supplier.name')
                    ->label('VENDOR / SUPPLIER')
                    ->icon('heroicon-m-building-storefront')
                    ->iconColor('primary')
                    ->weight('semibold')
                    ->searchable(),

                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('approver.name')
                    ->label('DISETUJUI OLEH')
                    ->placeholder('Belum Disetujui')
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('TANGGAL PEMBELIAN')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar-days')
                    ->iconColor('primary'),

                TextColumn::make('grand_total')
                    ->label('TOTAL TRANSAKSI')
                    ->money('IDR')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->weight('bold')
                    ->description(fn($record): string => ($record->total_quantity ?? 0) . " Item dipesan"),

                TextColumn::make('status')
                    ->label('STATUS')
                    ->badge()
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'draft' => 'gray',
                        'menunggu approval', 'pending' => 'warning',
                        'ordered', 'terbayar', 'lunas' => 'success',
                        'proses penerimaan' => 'info',
                        'cancelled', 'batal' => 'danger',
                        default => 'primary',
                    })
                    ->icon(fn(string $state): string => match (strtolower($state)) {
                        'ordered', 'terbayar', 'lunas' => 'heroicon-m-check-badge',
                        'menunggu approval', 'pending' => 'heroicon-m-clock',
                        'cancelled', 'batal' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-document-text',
                    })
                    ->formatStateUsing(fn(string $state) => strtoupper($state)),

                TextColumn::make('penerimaan_status')
                    ->label('STATUS PENERIMAAN')
                    ->badge()
                    ->state(function ($record) {
                        if (strtolower($record->status) === 'menunggu approval') return 'DALAM ANTRIAN';
                        if (!$record->due_date) return 'TGL JATUH TEMPO BELUM DIISI';
                        
                        $isOverdue = now()->startOfDay()->gt(Carbon::parse($record->due_date));
                        return $isOverdue ? 'PENERIMAAN TERLAMBAT' : 'PENERIMAAN SESUAI WAKTU';
                    })
                    ->color(fn($state): string => match ($state) {
                        'PENERIMAAN SESUAI WAKTU' => 'success',
                        'PENERIMAAN TERLAMBAT' => 'danger',
                        'DALAM ANTRIAN' => 'gray',
                        default => 'warning',
                    })
                    ->description(fn($record) => $record->due_date ? "Estimasi: " . Carbon::parse($record->due_date)->format('d M Y') : 'Set tanggal jatuh tempo'),
            ])
            ->headerActions([
                ActionsExportAction::make()
                    ->exporter(PurchaseExporter::class)
                    ->label('Export Excel')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                    ->formats([
                        \Filament\Actions\Exports\Enums\ExportFormat::Xlsx,
                    ]),
            ])
            ->filters([
                SelectFilter::make('supplier_id')
                    ->label('Pilih Supplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->columnSpan(1),

                SelectFilter::make('status')
                    ->label('Pilih Status')
                    ->options(fn() => Purchase::distinct()->pluck('status', 'status')->toArray())
                    ->searchable()
                    ->preload()
                    ->columnSpan(1),

                Filter::make('created_at_range')
                    ->form([
                        DatePicker::make('from')
                            ->default(now()->format('Y-m-d'))
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->default(now()->format('Y-m-d'))
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) $indicators[] = 'Mulai: ' . Carbon::parse($data['from'])->format('d M Y');
                        if ($data['until'] ?? null) $indicators[] = 'Sampai: ' . Carbon::parse($data['until'])->format('d M Y');
                        return $indicators;
                    })
                    ->columnSpan(2)
                    ->columns(2),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->actions([
                // PERBAIKAN UTAMA: Gunakan Action::make (Standard), BUKAN EditAction
                ActionsAction::make('edit_custom')
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning')
                    ->iconButton()
                    ->url(function ($record) {
                        // Cari transaksi terkait
                        $transaction = Transaction::where('purchase_id', $record->id)->first();

                        // Cek kategori PO-DIRECT
                        if ($transaction && $transaction->kategori === 'PO-DIRECT') {
                            return PurchaseResource::getUrl('direct-edit', ['record' => $record->id]);
                        }

                        // Default Edit standar
                        return PurchaseResource::getUrl('edit', ['record' => $record->id]);
                    }),
            ])
            ->emptyStateHeading('Belum ada data pembelian')
            ->emptyStateDescription('Silahkan buat Purchase Order baru untuk memulai transaksi.');
    }
}