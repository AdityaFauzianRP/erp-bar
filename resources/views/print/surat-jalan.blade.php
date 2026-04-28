<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $record->no_sj }}</title>
    <style>
        /* Pengaturan Kertas */
        @page {
            size: A4;
            margin: 0.8cm;
        }

        body {
            font-family: 'Inter', 'Helvetica', Arial, sans-serif;
            color: #000000;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 10pt;
            /* Ukuran font profesional */
            line-height: 1.3;
        }

        /* Helper Classes */
        .text-blue {
            color: #1e3a8a;
        }

        .text-muted {
            color: #000000;
        }

        .bold {
            font-weight: 700;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Header Layout */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .brand-title {
            font-size: 17pt;
            font-weight: 900;
            margin: 0;
            letter-spacing: -0.5px;
            color: #1e3a8a;
        }

        .doc-type {
            font-size: 18pt;
            font-weight: 300;
            letter-spacing: 4px;
            margin: 0;
            color: #000000;
        }

        /* Detail Boxes */
        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 15px;
        }

        .info-box {
            flex: 1;
            padding: 10px;
            border-top: 2px solid #1e3a8a;
            background: #f8fafc;
        }

        .info-label {
            font-size: 9pt;
            font-weight: 800;
            color: #000000;
            margin-bottom: 4px;
        }

        /* Table Styling */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .main-table th {
            border-top: 1px solid #1e3a8a;
            border-bottom: 1px solid #1e3a8a;
            padding: 8px 5px;
            font-weight: 800;
            font-size: 9pt;
            text-align: left;
            background: #fff;
        }

        .main-table td {
            padding: 8px 5px;
            /* border-bottom: 1px solid #f1f5f9; */
            vertical-align: top;
        }

        /* Signature Area */
        .footer-wrapper {
            margin-top: 40px;
            width: 100%;
        }

        .sig-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sig-cell {
            width: 25%;
            text-align: center;
            vertical-align: bottom;
            padding-bottom: 0;
        }

        .sig-name {
            margin-top: 100px;
            /* border-top: 1px solid #334155; */
            display: inline-block;
            width: 85%;
            padding-top: 5px;
            font-size: 9.5pt;
        }

        .notes-section {
            width: 60%;
            font-size: 9.5pt;
            border: 1px solid #e2e8f0;
            padding: 8px;
            margin-top: 15px;
            border-radius: 4px;
        }

        /* Print Optimization */
        @media print {
            .no-print {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <h1 class="brand-title">PT MITRA RAYA VAMILY</h1>
                <p class="text-muted" style="margin-top: 5px;">
                    Jl. Rajawali Timur No.108 42c, Ciroyom, Kec. Andir, Kota Bandung, Jawa Barat 40182
                </p>
            </td>
            <td class="text-right">
                <h2 class="doc-type uppercase">Surat Jalan</h2>
                <div style="margin-top: 10px;">
                    <span class="bold" style="font-size: 12pt;">#{{ $record->no_sj }}</span><br>
                    <span class="text-muted">Referensi Penjualan: {{ $record->proforma_invoice->number }}</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="info-grid">
        <div class="info-box">
            <div class="info-label uppercase">Tujuan Pengiriman</div>
            <div class="bold" style="font-size: 11pt;">
                {{ $record->proforma_invoice->customer_brand->customer_induk->name }}</div>
            <span class="text-muted">PO Customer: {{ $record->proforma_invoice->po_number }}</span>
            <div>{{ $record->proforma_invoice->customer_brand->brand_name }}</div>
            <div class="text-muted">{{ $record->proforma_invoice->customer_brand->nama_cabang }}</div>
        </div>
        <div class="info-box">
            <div class="info-label uppercase">Detail Pengiriman</div>
            <table style="width: 100%; font-size: 10pt;">
                <tr>
                    <td class="text-muted">Tgl Kirim</td>
                    <td class="text-right bold">{{ \Carbon\Carbon::parse($record->proforma_invoice->delivery_deadline)->format('d M Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Kendaraan</td>
                    <td class="text-right bold">{{ $record->vehicle_plate ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Pengemudi</td>
                    <td class="text-right bold">{{ $record->driver_name ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th class="text-left" style="width: 30px;">NO</th>
                <th style="width: 150px;">DESKRIPSI PRODUK</th>
                <th class="text-left" style="width: 70px;">QTY</th>
                <th class="text-left" style="width: 50px;">UNIT</th>
                <th class="text-left" style="width: 100px;">KONDISI BAIK</th>
                <th class="text-left" style="width: 100px;">KONDISI RUSAK</th>
            </tr>
        </thead>
        <tbody>
            @php $displayIndex = 1; @endphp {{-- Inisialisasi index manual agar nomor urut tetap rapi --}}
            @foreach ($record->items as $item)
                @if ($item->qty_shipped > 0)
                    <tr>
                        <td class="text-left text-muted">{{ $displayIndex++ }}</td>
                        <td>
                            <div class="bold">{{ $item->pi_item->product->name }}</div>
                            <div class="text-muted" style="font-size: 9pt;">SKU: {{ $item->pi_item->product->code }}
                            </div>
                        </td>
                        <td class="text-left bold" style="font-size: 11pt;">
                            {{ number_format($item->qty_shipped, 2, ',', '.') }}
                        </td>
                        <td class="text-left uppercase text-muted">`
                            {{ $item->pi_item->product->unit->name ?? 'PCS' }}
                        </td>
                        <td>
                            {{ $item->qty_received_good > 0 ? number_format($item->qty_received_good, 2, ',', '.') : '' }}
                        </td>
                        <td>
                            {{ $item->qty_wasted > 0 ? number_format($item->qty_wasted, 2, ',', '.') : '' }}
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div style="display: flex; gap: 20px;">
        <div class="notes-section">
            <div class="info-label uppercase">Catatan Khusus:</div>
            <div>{{ $record->notes ?? 'Tidak ada catatan tambahan.' }}</div>

        </div>

        <div style="flex: 1; text-align: right; padding-top: 15px;">
            <p class="text-muted" style="font-size: 9pt; font-style: italic;">
                * Mohon periksa kembali barang sebelum menandatangani.<br>
                Jika ada barang yang tidak sesuai
                Spesifikasi atau Rusak harap menginfokan 1 x 12 jam setelah penerimaan barang ke tim MRV.</p>
        </div>
    </div>

    <div class="footer-wrapper">
        <table class="sig-table">
            <tr>
                <td class="sig-cell">
                    <div class="info-label uppercase">Penerima <br> Tanda Tangan/Cap</div>
                    <div class="sig-name">(
                        ............................................................................... )</div>
                    <div style="text-transform: uppercase;">
                        {{ $record->proforma_invoice->customer_brand->customer_induk->name }}</div>
                </td>

                <td class="sig-cell">
                    <div class="info-label uppercase">Hormat Kami</div>
                    <div class="sig-name">(
                        ............................................................................... )</div>
                    <div style="text-transform: uppercase;">PT Mitra Raya Vamily</div>
                </td>

            </tr>
        </table>
    </div>

    <div class="footer-wrapper">
        <table class="sig-table">
            <tr>

                <td class="sig-cell">
                    <div class="info-label uppercase"></div>
                    <div class="sig-name">(
                        ............................................................................... )</div>
                    <div style="text-transform: uppercase;">Driver</div>
                </td>

                <td class="sig-cell">
                    <div class="info-label uppercase"></div>
                    <div class="sig-name">(
                        ............................................................................... )</div>
                    <div style="text-transform: uppercase;">Packer</div>
                </td>


            </tr>
        </table>
    </div>


</body>

</html>
