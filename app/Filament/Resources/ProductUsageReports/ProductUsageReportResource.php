<?php

namespace App\Filament\Resources\ProductUsageReports;

use App\Filament\Resources\ProductUsageReports\Pages\ManageProductUsageReports;
use App\Models\ProductUsageReport;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductUsageReportResource extends Resource
{
    protected static ?string $model = ProductUsageReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $navigationLabel = 'Laporan Detail Pemakaian';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $modelLabel = 'Laporan Detail Pemakaian';
    protected static ?string $pluralModelLabel = 'Laporan Detail Pemakaian';

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->select([
                    'product_id',
                    'nama_produk',
                    DB::raw('SUM(qty_received_good) as total_pemakaian'),
                    DB::raw('COUNT(delivery_item_id) as jumlah_transaksi'),
                    DB::raw('MAX(updated_at) as last_updated_at'),
                ])
                    ->groupBy('product_id', 'nama_produk')
            )
            ->columns([
                // TextColumn::make('product_id')
                //     ->label('ID Produk')
                //     ->sortable(),

                TextColumn::make('nama_produk')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_pemakaian')
                    ->label('Total Pemakaian')
                    ->numeric(decimalPlaces: 1)
                    ->sortable(),

                TextColumn::make('jumlah_transaksi')
                    ->label('Jumlah Transaksi')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('last_updated_at')
                    ->label('Update Terakhir')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('updated_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari'),
                        \Filament\Forms\Components\DatePicker::make('sampai'),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['dari'], fn($q) => $q->where('updated_at', '>=', $data['dari']))
                            ->when($data['sampai'], fn($q) => $q->where('updated_at', '<=', $data['sampai']));
                    })
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->actions([
                Action::make('view_customers')
                    ->label('Detail Customer')
                    ->icon('heroicon-m-user-group')
                    ->color('success')
                    ->modalHeading(fn($record) => "Detail Customer: " . $record->nama_produk)
                    ->modalSubmitAction(false)
                    ->modalContent(function ($record, Table $table) {
                        // 1. Ambil range tanggal dari filter Filament
                        $dateFilter = $table->getFilter('updated_at')->getState();
                        $dari = $dateFilter['dari'] ?? '2000-01-01';
                        $sampai = $dateFilter['sampai'] ?? '2099-12-31';

                        // 2. Gunakan DB::table untuk menduplikasi query SELECT Anda secara presisi
                        $customers = DB::table('delivery_items as di')
                            ->join('proforma_invoice_items as pii', 'di.proforma_invoice_item_id', '=', 'pii.id')
                            ->join('proforma_invoices as pi', 'pii.proforma_invoice_id', '=', 'pi.id')
                            ->join('customer_brands as c', 'pi.customer_brand_id', '=', 'c.id')
                            ->join('products as p', 'pii.product_id', '=', 'p.id')
                            ->join('units as u', 'p.unit_id', '=', 'u.id') // Tambahkan join units
                            ->where('p.id', $record->product_id)
                            ->whereBetween('di.updated_at', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])
                            ->select([
                                'di.id as delivery_item_id',
                                'c.brand_name as nama_customer',
                                'p.name AS nama_produk',
                                'di.qty_shipped as total_pemakaian_customer',
                                'u.name as nama_satuan',        // Tambahkan select unit name
                                'di.updated_at as waktu_transaksi'
                            ])
                            ->orderBy('di.updated_at', 'desc')
                            ->get();

                        return view('filament.components.customer-detail-table', [
                            'customers' => $customers
                        ]);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProductUsageReports::route('/'),
        ];
    }
}
