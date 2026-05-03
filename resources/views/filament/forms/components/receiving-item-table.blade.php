<div class="erp-wrapper" x-data="{
    page: 1,
    perPage: 5,
    total: {{ count($getState() ?? []) }},
    get totalPages() { return Math.ceil(this.total / this.perPage) }
}">
    <table class="erp-table">
        <thead>
            <tr>
                {{-- Semua Header Rata Kiri --}}
                <th class="text-left">Deskripsi Barang</th>
                <th class="text-left" style="width: 120px;">Qty Order</th>
                <th class="text-left" style="width: 120px;">Kondisi Baik</th>
                <th class="text-left" style="width: 120px;">Kondisi Reject</th>
                <th class="text-left">Catatan / Alasan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $items = $getState() ?? [];
                $index = 0;
            @endphp
            @forelse($items as $uuid => $item)
                @php
                    $index++;
                    // Ambil nama produk dari model jika di array tidak ada 'product_name'
                    $productName =
                        $item['product_name'] ??
                        (\App\Models\Product::find($item['product_id'])?->name ?? 'Produk Tidak Diketahui');

                    $productCode =
                        $item['product_code'] ??
                        (\App\Models\Product::find($item['product_id'])?->code ?? 'Produk Tidak Diketahui');

                    // Hilangkan .00 dengan casting ke float atau number_format
                    $qtyOrder = (float) ($item['qty_order'] ?? 0);
                @endphp
                <tr wire:key="item-{{ $uuid }}" x-show="page === Math.ceil({{ $index }} / perPage)"
                    x-transition:enter.duration.300ms>

                    {{-- Tambahkan data-label untuk mobile --}}
                    <td class="text-left" data-label="Barang">
                        <div class="item-box">
                            <span class="item-name">{{ $productName }}</span>
                            <span class="item-sub">Code Produk: {{ $productCode }}</span>
                        </div>
                    </td>

                    {{-- Qty Order (Sudah bersih dari .00) --}}
                    <td class="text-left" data-label="Qty Order">
                        <span class="qty-badge">{{ $qtyOrder }}</span>
                    </td>

                    <td class="text-left" data-label="Kondisi Baik">
                        <div class="input-wrapper">
                            <input type="number" wire:model="data.items.{{ $uuid }}.qty_received"
                                class="input-field input-blue" placeholder="0" onfocus="this.select()">
                        </div>
                    </td>

                    <td class="text-left" data-label="Kondisi Reject">
                        <div class="input-wrapper">
                            <input type="number" wire:model="data.items.{{ $uuid }}.qty_rejected"
                                class="input-field input-ghost" placeholder="0" onfocus="this.select()">
                        </div>
                    </td>

                    <td class="text-left" data-label="Catatan">
                        <input type="text" wire:model="data.items.{{ $uuid }}.reject_reason"
                            class="input-text-modern" placeholder="Catatan...">
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-row">
                        <div class="empty-container">
                            <span class="empty-text">Belum ada item. Silakan pilih Referensi PO terlebih dahulu.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination muncul hanya jika data > 5 --}}
    <template x-if="totalPages > 1">
        <div class="erp-pagination">
            <div class="pagination-info">
                Menampilkan halaman <span x-text="page" class="font-bold"></span> dari <span x-text="totalPages"
                    class="font-bold"></span>
            </div>
            <div class="pagination-buttons">
                <button type="button" @click="page--" :disabled="page === 1" class="p-btn">
                    &laquo; SBLM
                </button>
                <button type="button" @click="page++" :disabled="page === totalPages" class="p-btn">
                    LANJUT &raquo;
                </button>
            </div>
        </div>
    </template>
</div>

<style>
    /* --- CSS EXISTING (Sudah dirapikan) --- */
    .erp-wrapper {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .erp-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .erp-table thead {
        background: #f8fafc;
        border-bottom: 2px solid #3b82f6;
    }

    .erp-table th {
        padding: 14px 20px;
        font-size: 11px;
        font-weight: 700;
        color: #1e3a8a;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .erp-table td {
        padding: 12px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .text-left {
        text-align: left !important;
    }

    .item-name {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        display: block;
    }

    .item-sub {
        font-size: 11px;
        color: #64748b;
    }

    .qty-badge {
        display: inline-block;
        padding: 4px 12px;
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
    }

    .input-field {
        width: 100%;
        max-width: 90px;
        height: 38px;
        padding-left: 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-weight: 600;
    }

    .input-blue {
        color: #2563eb;
        background: #fff;
    }

    .input-ghost {
        color: #64748b;
        background: #f8fafc;
    }

    .input-text-modern {
        width: 100%;
        height: 38px;
        padding: 0 12px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .erp-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        background: #f8fafc;
    }

    /* --- RESPONSIVE LOGIC (MOBILE) --- */
    @media (max-width: 768px) {

        /* Sembunyikan Header Tabel */
        .erp-table thead {
            display: none;
        }

        /* Ubah Table Row menjadi Card */
        .erp-table tbody tr {
            display: block;
            padding: 15px;
            border-bottom: 8px solid #f1f5f9;
            /* Pemisah antar kartu */
        }

        .erp-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border: none;
            width: 100% !important;
            text-align: right !important;
        }

        /* Tambahkan Label di sebelah kiri menggunakan pseudo-element */
        .erp-table td::before {
            content: attr(data-label);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            text-align: left;
            flex: 1;
        }

        /* Penyesuaian khusus elemen di dalam TD */
        .erp-table td .item-box {
            text-align: right;
            flex: 2;
        }

        .erp-table td .input-wrapper,
        .erp-table td .input-text-modern {
            max-width: 160px;
        }

        .erp-pagination {
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }
    }
</style>
