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

class OperationalItemReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, ShouldAutoSize
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
        return ['No', 'Tanggal', 'Kategori Pengeluaran', 'Item', 'Qty', 'Satuan', 'Subtotal'];
    }

    public function map($row): array
    {
        return [
            $row->No,
            $row->Tanggal ? \Carbon\Carbon::parse($row->Tanggal)->format('d/m/Y') : '-',
            $row->{'Kategori Pengeluaran'} ?? '-',
            $row->Item,
            (float) $row->Qty,
            $row->Satuan,
            (float) $row->Subtotal,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'LAPORAN DETAIL ITEM OPERASIONAL');
        $sheet->mergeCells('A2:G2');
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
