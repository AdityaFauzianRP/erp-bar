<?php

namespace App\Exports;

use App\Models\ExecSummaryFact;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExecutiveSummaryExport implements WithEvents, ShouldAutoSize
{
    protected $dari;
    protected $sampai;

    public function __construct($dari, $sampai)
    {
        $this->dari = $dari;
        $this->sampai = $sampai;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Ambil Data dari Model menggunakan static function total() Anda
                $s_paid    = ExecSummaryFact::total('sales_paid', $this->dari, $this->sampai);
                $s_unpaid  = ExecSummaryFact::total('sales_unpaid', $this->dari, $this->sampai);
                $p_paid    = ExecSummaryFact::total('purchase_paid', $this->dari, $this->sampai);
                $p_unpaid  = ExecSummaryFact::total('purchase_unpaid', $this->dari, $this->sampai);
                $w_sales   = ExecSummaryFact::total('waste_sales', $this->dari, $this->sampai);
                $w_wh      = ExecSummaryFact::total('waste_warehouse', $this->dari, $this->sampai);
                $w_purch   = ExecSummaryFact::total('waste_purchase', $this->dari, $this->sampai);
                $opex      = ExecSummaryFact::total('pengeluaran_operational', $this->dari, $this->sampai);

                // Kalkulasi Laporan
                $total_sales    = $s_paid + $s_unpaid;
                $total_purchase = $p_paid + $p_unpaid;
                $total_waste    = $w_sales + $w_wh + $w_purch;
                $laba_kotor     = $total_sales - $total_purchase - $total_waste;
                $laba_bersih    = $laba_kotor - $opex;

                // 2. Desain Header
                $sheet->mergeCells('A1:B1');
                $sheet->setCellValue('A1', 'EXECUTIVE SUMMARY');
                $sheet->mergeCells('A2:B2');
                $sheet->setCellValue('A2', \Carbon\Carbon::parse($this->dari)->format('d F Y') . ' - ' . \Carbon\Carbon::parse($this->sampai)->format('d F Y'));

                // 3. Header Tabel
                $sheet->setCellValue('A4', 'KETERANGAN')->setCellValue('B4', 'NILAI');

                // 4. Isi Data Sesuai Gambar
                $rows = [
                    ['Penjualan Sudah Dibayar', $s_paid],
                    ['Penjualan Belum Dibayar', $s_unpaid],
                    ['Total Penjualan', $total_sales, true], // Bold
                    ['', ''], // Spacer
                    ['Pembelian Sudah Dibayar', $p_paid],
                    ['Pembelian Belum Dibayar', $p_unpaid],
                    ['Total Pembelian', $total_purchase, true],
                    ['', ''],
                    ['Barang Rusak/Busuk Penjualan', $w_sales],
                    ['Barang Rusak/Busuk Gudang', $w_wh],
                    ['Barang Rusak/Busuk Pembelian', $w_purch],
                    ['Total Barang Rusak/Busuk', $total_waste, true],
                    ['', ''],
                    ['Laba Kotor', $laba_kotor, true],
                    ['Pengeluaran Operasional', $opex],
                    ['Laba Bersih', $laba_bersih, true],
                ];

                $currentRow = 5;
                foreach ($rows as $row) {
                    $sheet->setCellValue('A' . $currentRow, $row[0]);
                    if ($row[1] !== '') {
                        $sheet->setCellValue('B' . $currentRow, $row[1]);
                        $sheet->getStyle('B' . $currentRow)->getNumberFormat()->setFormatCode('Rp #,##0');
                    }

                    if (isset($row[2]) && $row[2]) {
                        $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->getFont()->setBold(true);
                    }
                    $currentRow++;
                }

                // 5. Styling
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A4:B4')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
                $sheet->getStyle('A4:B4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E40AF');

                // Border untuk area tabel
                $sheet->getStyle('A4:B' . ($currentRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }
        ];
    }
}
