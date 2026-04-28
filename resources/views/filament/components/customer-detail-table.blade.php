<style>
    .custom-table-container {
        font-family: 'Inter', sans-serif;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        margin: 10px;
    }

    .my-custom-table {
        width: 100%;
        border-collapse: collapse;
        background-color: #ffffff;
        font-size: 14px;
        color: #374151;
    }

    .my-custom-table thead {
        background-color: #f9fafb;
        border-bottom: 2px solid #f3f4f6;
    }

    .my-custom-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #111827;
    }

    .my-custom-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f3f4f6;
    }

    .my-custom-table tbody tr:hover {
        background-color: #fcfcfc;
    }

    .my-custom-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Styling khusus kolom */
    .col-qty {
        font-family: 'Courier New', Courier, monospace;
        font-weight: bold;
        text-align: right;
        color: #1a56db;
    }

    .col-satuan {
        font-size: 12px;
        color: #6b7280;
        font-style: italic;
    }

    .badge-produk {
        background-color: #eff6ff;
        color: #1e40af;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid #dbeafe;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #9ca3af;
        font-style: italic;
    }
</style>

<div class="custom-table-container">
    <table class="my-custom-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Produk</th>
                <th style="text-align: right;">Qty</th>
                <th>Satuan</th>
                <th>Waktu Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $item)
            <tr>
                <td style="font-weight: 500;">{{ $item->nama_customer ?? 'N/A' }}</td>
                <td>
                    <span class="badge-produk">{{ $item->nama_produk ?? 'N/A' }}</span>
                </td>
                <td class="col-qty">
                    {{ number_format($item->total_pemakaian_customer, 2) }}
                </td>
                <td class="col-satuan">
                    {{ $item->nama_satuan }}
                </td>
                <td style="color: #6b7280;">
                    {{ \Carbon\Carbon::parse($item->waktu_transaksi)->format('d/m/Y H:i') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="empty-state">
                    Tidak ada rekam jejak ditemukan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>