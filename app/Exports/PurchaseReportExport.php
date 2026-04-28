<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PurchaseReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, ShouldAutoSize
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
        return 'A5';
    }

    public function headings(): array
    {
        return ['No', 'Tanggal Pembelian', 'No Pembelian', 'Supplier', 'Jumlah Order', 'Total Order', 'Status'];
    }

    public function map($row): array
    {
        return [
            $row->No,
            $row->{'Tanggal Pembelian'} ? \Carbon\Carbon::parse($row->{'Tanggal Pembelian'})->format('d/m/Y') : '-',
            $row->{'No Pembelian'},
            $row->{'Supplier'},
            $row->{'Jumlah Order'} ?? 0,
            (float) $row->{'Total Order'},
            strtoupper($row->Status),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Judul Laporan
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'LAPORAN PEMBELIAN');
        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', $this->periode);

        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        // Styling Header Tabel
        return [
            5 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E40AF']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
