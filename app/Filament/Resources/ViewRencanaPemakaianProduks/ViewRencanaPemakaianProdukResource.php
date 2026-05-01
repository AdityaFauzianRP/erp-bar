<?php

namespace App\Filament\Resources\ViewRencanaPemakaianProduks;

use App\Filament\Resources\ViewRencanaPemakaianProduks\Pages\ManageViewRencanaPemakaianProduks;
use App\Models\ViewRencanaPemakaianProduk;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Tables\Enums\FiltersLayout;

class ViewRencanaPemakaianProdukResource extends Resource
{
    protected static ?string $model = ViewRencanaPemakaianProduk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Laporan Rencana Pemakaian Produk';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Laporan Rencana Pemakaian Produk';
    protected static ?string $pluralModelLabel = 'Laporan Rencana Pemakaian Produk';

    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                // TextColumn::make('id_product')
                //     ->label('id')
                //     ->sortable()
                //     ->searchable(),

                // TextColumn::make('kode_produk')
                //     ->label('Kode')
                //     ->sortable()
                //     ->searchable(),

                TextColumn::make('nama_produk')
                    ->label('Produk')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total_qty_pesanan')
                    ->label('Total Qty')
                    ->numeric(decimalPlaces: 1)
                    ->alignRight(),

                TextColumn::make('satuan')
                    ->label('Satuan')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('jumlah_invoice')
                    ->label('Inv')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('delivery_deadline')
                    ->label('Deadline')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn($state) => $state->isPast() ? 'danger' : 'success'),
            ])
            ->defaultSort('id', 'asc')
            ->filters([
                // Filter Range Tanggal Delivery
                Filter::make('delivery_deadline')
                    ->form([
                        DatePicker::make('from')
                            ->label('Deadline Dari'),
                        DatePicker::make('until')
                            ->label('Deadline Sampai'),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('delivery_deadline', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('delivery_deadline', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'Mulai: ' . \Carbon\Carbon::parse($data['from'])->format('d M Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['until'])->format('d M Y');
                        }
                        return $indicators;
                    })
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('view_brand_details')
                    ->label('Detail Brand')
                    ->icon('heroicon-o-presentation-chart-line')
                    ->modalHeading(fn($record) => "Rincian Brand: " . $record->nama_produk)
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist(function ($record) {
                        // Jalankan Query
                        $details = DB::table('proforma_invoice_items as pii')
                            ->join('proforma_invoices as pi', 'pii.proforma_invoice_id', '=', 'pi.id')
                            ->join('products as p', 'pii.product_id', '=', 'p.id')
                            ->leftJoin('units as u', 'p.unit_id', '=', 'u.id')
                            ->join('customer_brands as cb', 'pi.customer_brand_id', '=', 'cb.id')
                            ->select(
                                'cb.brand_name as nama_brand',
                                DB::raw('SUM(pii.qty) as total_qty_order'),
                                'u.short_name as satuan',
                                DB::raw('COUNT(DISTINCT pi.id) as jumlah_transaksi')
                            )
                            // 1. Filter berdasarkan produk
                            ->where('p.id', $record->id_product)

                            // 2. GANTI DISINI: Filter tanggal persis sesuai baris yang diklik
                            ->whereDate('pi.delivery_deadline', $record->delivery_deadline)

                            // 3. Filter status
                            ->whereNotIn('pi.status', ['draft', 'cancelled'])

                            ->groupBy('cb.brand_name', 'u.short_name')
                            ->orderBy('total_qty_order', 'desc')
                            ->get();

                        return [
                            ViewEntry::make('brand_details')
                                ->view('filament.components.brand-detail-table')
                                ->viewData([
                                    'brandDetails' => $details,
                                    'targetDate' => $record->delivery_deadline // Opsional: buat nampilin tgl di blade
                                ])
                        ];
                    })
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageViewRencanaPemakaianProduks::route('/'),
        ];
    }
}
