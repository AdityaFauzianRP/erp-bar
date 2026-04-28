<?php

namespace App\Filament\Resources\RealisasiItemReports\Pages;

use App\Exports\RealisasiItemExport;
use App\Filament\Resources\RealisasiItemReports\RealisasiItemReportResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Maatwebsite\Excel\Facades\Excel;

class ManageRealisasiItemReports extends ManageRecords
{
    protected static string $resource = RealisasiItemReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Download Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // Ambil data yang sedang di-filter di tabel
                    $data = $this->getFilteredTableQuery()->get();

                    // Logic teks periode untuk judul excel
                    $filters = $this->tableFilters;
                    $dari = $filters['tanggal']['dari_tanggal'] ?? '-';
                    $sampai = $filters['tanggal']['sampai_tanggal'] ?? '-';
                    $periodeText = "$dari s/d $sampai";

                    return Excel::download(
                        new RealisasiItemExport($data, $periodeText),
                        'Laporan_Realisasi_Order_' . now()->format('Y-m-d') . '.xlsx'
                    );
                }),
        ];
    }
}
