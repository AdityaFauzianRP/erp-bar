<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Produk', Product::count())
                ->description('Jumlah produk terdaftar')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),
                
            Stat::make('Total Pembelian (PO)', Purchase::count())
                ->description('Total dokumen Purchase Order')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),
                
            Stat::make('Total Transaksi', Transaction::count())
                ->description('Jumlah seluruh transaksi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
                
            Stat::make('Pengeluaran (Total Akhir)', 'Rp ' . Number::format(Transaction::sum('total_akhir') ?? 0, locale: 'id'))
                ->description('Total nilai pengeluaran/transaksi')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger'),
        ];
    }
}
