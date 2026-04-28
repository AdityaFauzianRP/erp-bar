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

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, ShouldAutoSize
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
        return ['No', 'Tanggal Pengiriman', 'No Penjualan', 'Faktur', 'Nama Perusahaan', 'Cabang', 'Jumlah Order', 'Total HPP', 'Total Order', 'Note', 'Status'];
    }

    public function map($row): array
    {
        return [
            $row->{'ID'} ?? $row->{'No'} ?? $row->id, // Coba beberapa kemungkinan nama kolom untuk nomor urut
            $row->{'Tanggal Pengiriman'} ? \Carbon\Carbon::parse($row->{'Tanggal Pengiriman'})->format('d/m/Y') : '-',
            $row->{'No Penjualan'},
            $row->{'Faktur'} ?? 'Belum Ada',
            $row->{'Nama Perusahaan'},
            $row->{'Cabang'},
            $row->{'Jumlah Order'} ?? 0,
            (float) $row->{'Total HPP'},
            (float) $row->{'Total Order'},
            $row->{'Note'} ?? '-',
            strtoupper($row->{'Status'}),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Judul Utama
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'LAPORAN PENJUALAN');
        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', $this->periode);

        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        // 2. Style Header Tabel (Baris 5)
        $headerRange = 'A5:K5';
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
        $sheet->getStyle("A5:K{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [];
    }
}
