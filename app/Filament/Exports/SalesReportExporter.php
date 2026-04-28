<?php

namespace App\Filament\Exports;

use App\Models\SalesReport;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Carbon;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Style;

class SalesReportExporter extends Exporter
{
    protected static ?string $model = SalesReport::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('No')
                ->label('No'),

            ExportColumn::make('Tanggal Pengiriman')
                ->label('Tanggal Pengiriman')
                ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('d/m/Y') : '-'),

            ExportColumn::make('No Penjualan')
                ->label('No Penjualan'),

            ExportColumn::make('Faktur')
                ->label('Faktur'),

            ExportColumn::make('Nama Perusahaan')
                ->label('Nama Perusahaan'),

            ExportColumn::make('Cabang')
                ->label('Cabang'),

            ExportColumn::make('Jumlah Order') // Pastikan field ini ada di model/view Anda
                ->label('Jumlah Order')
                ->formatStateUsing(fn($state) => (int) $state),

            ExportColumn::make('Total HPP')
                ->label('Total HPP')
                ->formatStateUsing(fn($state) => (float) $state),

            ExportColumn::make('Total Order')
                ->label('Total Order')
                ->formatStateUsing(fn($state) => (float) $state),

            ExportColumn::make('Note')
                ->label('Note'),

            ExportColumn::make('Status')
                ->label('Status')
                ->formatStateUsing(fn($state) => strtoupper($state)),
        ];
    }

    /**
     * Header atas (Row 1-4) sesuai gambar yang Anda kirim
     */
    public function getHeaderRows(): array
    {
        $startDate = request()->input('tableFilters.Tanggal_Pengiriman.dari') ?? '01 Januari 2026';
        $endDate = request()->input('tableFilters.Tanggal_Pengiriman.sampai') ?? '28 Januari 2026';

        return [
            ['LAPORAN PENJUALAN'], // Row 1
            [$startDate . ' - ' . $endDate], // Row 2
            [''], // Row 3 (Spasi)
            [''], // Row 4 (Spasi)
        ];
    }

    /**
     * Styling Header Tabel (Row 5 - Berwarna Biru Gelap)
     */
    public function getXlsxHeaderCellStyle(): ?Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontSize(11)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('1E40AF') // Biru (Tailwind Blue 800)
            ->setCellAlignment(CellAlignment::CENTER);
    }

    /**
     * Styling Isi Tabel (Row 6 ke bawah - Border tipis & teks rapi)
     */
    public function getXlsxCellStyle(): ?Style
    {
        return (new Style())
            ->setFontSize(10)
            ->setCellAlignment(CellAlignment::LEFT);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export Laporan Penjualan Selesai. ' . number_format($export->successful_rows) . ' baris diproses.';
    }
}
