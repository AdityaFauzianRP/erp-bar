<style>
    /* Container Styling */
    .erp-container {
        overflow: hidden;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
    }

    .erp-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
        text-align: left;
    }

    /* Header Styling */
    .erp-thead tr {
        background-color: rgba(249, 250, 251, 0.5);
        backdrop-filter: blur(4px);
        border-bottom: 1px solid #e5e7eb;
    }

    .erp-thead th {
        padding: 1rem 1.5rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
        color: #374151;
    }

    /* Body Styling */
    .erp-tbody {
        background-color: #ffffff;
    }

    .erp-tbody tr {
        transition: background-color 0.2s;
        border-bottom: 1px solid #f3f4f6;
    }

    .erp-tbody tr:last-child {
        border-bottom: none;
    }

    .erp-tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.05);
        /* Soft Blue Hover */
    }

    .erp-tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
    }

    /* Brand Cell Component */
    .brand-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .brand-avatar {
        display: flex;
        height: 2rem;
        width: 2rem;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background-color: #f3f4f6;
        color: #6b7280;
        font-weight: 700;
        transition: all 0.2s;
    }

    .erp-tbody tr:hover .brand-avatar {
        background-color: #dbeafe;
        color: #2563eb;
    }

    .brand-name {
        font-weight: 500;
        color: #111827;
    }

    /* Data Formatting */
    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .qty-font {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-weight: 700;
        color: #111827;
    }

    /* Badges */
    .badge-unit {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        background-color: #eff6ff;
        color: #1d4ed8;
        border: 1px solid rgba(29, 78, 216, 0.1);
    }

    .badge-inv {
        display: inline-flex;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        background-color: #f3f4f6;
        color: #4b5563;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Empty State */
    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
        color: #9ca3af;
        font-style: italic;
    }

    .empty-icon {
        width: 3rem;
        height: 3rem;
        margin: 0 auto 0.5rem;
        opacity: 0.5;
    }

    /* DARK MODE SUPPORT */
    @media (prefers-color-scheme: dark) {
        .erp-container {
            background-color: #111827;
            border-color: #374151;
        }

        .erp-thead tr {
            background-color: rgba(31, 41, 55, 0.5);
            border-color: #374151;
        }

        .erp-thead th {
            color: #e5e7eb;
        }

        .erp-tbody {
            background-color: #111827;
        }

        .erp-tbody tr {
            border-color: #1f2937;
        }

        .erp-tbody tr:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }

        .brand-avatar {
            background-color: #1f2937;
            color: #9ca3af;
        }

        .brand-name {
            color: #ffffff;
        }

        .qty-font {
            color: #f9fafb;
        }

        .badge-unit {
            background-color: rgba(30, 64, 175, 0.2);
            color: #60a5fa;
            border-color: rgba(96, 165, 250, 0.3);
        }

        .badge-inv {
            background-color: #1f2937;
            color: #9ca3af;
        }
    }
</style>

<div class="erp-container">
    <table class="erp-table">
        <thead class="erp-thead">
            <tr>
                <th>Nama Brand</th>
                <th class="text-right">Total Qty</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Jml Transaksi</th>
            </tr>
        </thead>
        <tbody class="erp-tbody">
            @forelse($brandDetails as $item)
                <tr>
                    <td>
                        <div class="brand-wrapper">
                            <div class="brand-avatar">
                                {{ substr($item->nama_brand, 0, 1) }}
                            </div>
                            <span class="brand-name">{{ $item->nama_brand }}</span>
                        </div>
                    </td>
                    <td class="text-right">
                        <span class="qty-font">{{ number_format($item->total_qty_order, 1) }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge-unit">{{ $item->satuan }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge-inv">{{ $item->jumlah_transaksi }} Inv</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <svg class="empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p>Belum ada riwayat pesanan untuk item ini.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
