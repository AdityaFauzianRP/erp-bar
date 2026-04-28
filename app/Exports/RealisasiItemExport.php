<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RealisasiItemExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, ShouldAutoSize
{
    protected $data;
    protected $periode;

    public function __construct($data, $periode = '')
    {
        $this->data = $data;
        $this->periode = $periode;
    }

    public function collection()
    {
        return $this->data;
    }

    // Mulai data dari baris ke-5 agar ada ruang untuk Judul & Periode
    public function startCell(): string
    {
        return 'A5';
    }

    // Header Tabel
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Barang',
            'Satuan',
            'Qty Pesanan',
            'Qty Dikirim',
            'Qty Bagus',
            'Sisa Kirim',
            '% Realisasi',
        ];
    }

    // Mapping Data per Kolom
    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->tanggal->format('d/m/Y'),
            $row->nama_barang,
            $row->satuan,
            $row->qty_pesanan,
            $row->qty_dikirim,
            $row->qty_bagus,
            $row->sisa_kirim,
            $row->persentase_realisasi . '%',
        ];
    }

    // Styling Layout (Header & Judul)
    public function styles(Worksheet $sheet)
    {
        // Judul Laporan
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'LAPORAN REALISASI ITEM ORDER');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Periode
        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', 'Periode: ' . $this->periode);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        // Styling Header Tabel (Baris 5)
        return [
            5 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2F5597'] // Biru tua seperti contoh
                ],
            ],
        ];
    }
}