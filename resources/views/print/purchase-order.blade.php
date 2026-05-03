<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Purchase Order - {{ $record->purchase_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 9pt;
        }

        .text-main {
            color: #1e3a8a !important;
        }

        .h-title {
            font-size: 18pt;
            font-weight: 800;
            margin: 0;
        }

        .label-text {
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
        }

        /* Table Styling */
        .table-modern {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #1e3a8a;
        }

        .table-modern th {
            background-color: #1e3a8a !important;
            color: white !important;
            padding: 10px;
            text-align: left;
            -webkit-print-color-adjust: exact;
        }

        .table-modern td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Summary Box */
        .summary-wrapper {
            float: right;
            width: 250px;
            margin-top: 20px;
            border: 1px solid #1e3a8a;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            padding: 8px;
        }

        .grand-total-item {
            background-color: #1e3a8a !important;
            color: white !important;
            padding: 10px;
            font-weight: 800;
            -webkit-print-color-adjust: exact;
        }

        .footer-sign {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body onload="window.print()"> {{-- Otomatis buka dialog print saat load --}}
    <div style="padding: 20px;">
        {{-- Header --}}
        <table style="width: 100%; border: none; margin-bottom: 20px;">
            <tr>
                <td style="width: 50%;">
                    <h1 class="h-title text-main">PT MITRA RAYA VAMILY</h1>
                    <p style="font-size: 8pt; color: #64748b;">Jl. Rajawali Timur No.108 42c, Ciroyom, Kec. Andir, Kota Bandung, Jawa Barat 40182</p>
                </td>
                <td style="text-align: right;">
                    <h2 style="margin: 0; color: #3b82f6; letter-spacing: 2px;">PURCHASE ORDER</h2>
                    <p style="font-weight: bold;">#{{ $record->po_number }}</p>
                </td>
            </tr>
        </table>

        {{-- Info Supplier & Order --}}
        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div style="flex: 1; border: 1px solid #e2e8f0; padding: 10px; background: #f8fafc;">
                <div class="label-text">Supplier Information</div>
                <div style="font-weight: bold; margin-top: 5px;">{{ $record->supplier->name ?? '-' }}</div>
                <div style="font-size: 8pt;">{{ $record->supplier->address ?? '-' }}</div>
            </div>
            <div style="flex: 1; border: 1px solid #e2e8f0; padding: 10px; background: #f8fafc;">
                <div class="label-text">Order Details</div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <span>Date:</span> <strong>{{ $record->created_at->format('d/m/Y') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Status:</span> <strong>{{ $record->status }}</strong>
                </div>
            </div>
        </div>

        {{-- Table Items --}}
        <table class="table-modern">
            <thead>
                <tr>
                    <th>DESCRIPTION</th>
                    <th style="text-align: right;">PRICE</th>
                    <th style="text-align: center;">QTY</th>
                    <th style="text-align: right;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->name ?? $item->name }}</strong><br>
                            <small>{{ $item->product->code ?? $item->code }}</small>
                        </td>
                        <td style="text-align: right;">{{ number_format($item->unit_price) }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ number_format($item->subtotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Grand Total --}}
        <div class="summary-wrapper">
            <div class="total-item">
                <span>Subtotal</span>
                <strong>Rp {{ number_format($record->subtotal) }}</strong>
            </div>
            <div class="total-item">
                <span>Tax ({{ $record->tax_rate }}%)</span>
                <strong>Rp {{ number_format($record->tax_amount) }}</strong>
            </div>
            <div class="grand-total-item">
                <span>GRAND TOTAL</span>
                <span>Rp {{ number_format($record->grand_total) }}</span>
            </div>
        </div>

        <div style="clear: both;"></div>

        {{-- Tanda Tangan --}}
        <div class="footer-sign">
            <div style="text-align: center; width: 150px;">
                <div class="label-text" style="margin-bottom: 100px;">Prepared By</div>
                <div style="border-top: 1px solid #000; padding-top: 5px;">{{ auth()->user()->name }}</div>
            </div>
            <div style="text-align: center; width: 150px;">
                <div class="label-text" style="margin-bottom: 100px;">Authorized By</div>
                <div style="border-top: 1px solid #000; padding-top: 5px;">Finance Director</div>
            </div>
        </div>
    </div>
</body>

</html>
