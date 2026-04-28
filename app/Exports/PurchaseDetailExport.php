<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PurchaseDetailExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, ShouldAutoSize
{
    protected $data;
    protected $periode;

    public function __construct($data, $periode)
    {
        $this->data = $data;
        $this->periode = $periode;
    }

    public function collection()
    {
        return $this->data;
    }

    public function startCell(): string
    {
        return 'A5'; // Tabel mulai di baris 5 agar Row 1-4 bisa dipakai Judul
    }

    public function headings(): array
    {
        return ['No', 'Tanggal Pembelian', 'No Pembelian', 'Supplier', 'Nama Barang', 'Harga Beli', 'Qty', 'Satuan', 'Subtotal Order'];
    }

    public function map($row): array
    {
        return [
            $row->{'ID'} ?? $row->{'No'} ?? $row->id, // Coba beberapa kemungkinan nama kolom untuk nomor urut
            $row->{'Tanggal Pembelian'} ? \Carbon\Carbon::parse($row->{'Tanggal Pembelian'})->format('d/m/Y') : '-',
            $row->{'No Pembelian'},
            $row->{'Supplier'},
            $row->{'nama_barang'},
            $row->{'Harga Beli'},
            $row->{'qty'},
            $row->{'satuan'},
            $row->{'Subtotal Order'},
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Judul Utama
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'LAPORAN PEMBELIAN DETAIL');
        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', $this->periode);

        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        // 2. Style Header Tabel (Baris 5)
        $headerRange = 'A5:I5';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E40AF'], // Biru Gelap
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // 3. Border untuk semua data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A5:I{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [];
    }
}