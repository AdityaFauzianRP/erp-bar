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

class SalesDetailExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, ShouldAutoSize
{
    protected $data;
    protected $periode;

    public function __construct($data, $periode) { $this->data = $data; $this->periode = $periode; }

    public function collection() { return $this->data; }

    public function startCell(): string { return 'A5'; }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'No Penjualan', 'Customer', 'Cabang', 'Nama Barang', 'Satuan', 'Qty', 'HPP', 'Harga Jual', 'Subtotal HPP', 'Subtotal Order'];
    }

    public function map($row): array
    {
        return [
            $row->No,
            $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') : '-',
            $row->{'No Penjualan'},
            $row->{'Nama Perusahaan'},
            $row->Cabang ?? '-',
            $row->{'Nama Barang'},
            $row->satuan,
            $row->qty,
            (float) $row->HPP,
            (float) $row->{'Harga Jual'},
            (float) $row->{'Subtotal HPP'},
            (float) $row->{'Subtotal Order'},
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'LAPORAN PENJUALAN DETAIL');
        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', $this->periode);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        return [
            5 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E40AF']],
            ],
        ];
    }
}