<?php

namespace App\Filament\Exports;

use App\Models\Purchase;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class PurchaseExporter extends Exporter
{
    protected static ?string $model = Purchase::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('po_number')->label('Nomor PO'),
            ExportColumn::make('supplier.name')->label('Vendor'),
            ExportColumn::make('grand_total')->label('Total Transaksi'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('created_at')->label('Tanggal Input'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your purchase export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
