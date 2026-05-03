@php
    $isDisabled = $getRecord() && $getRecord()->status === 'completed';

    // Mengambil semua produk beserta satuan untuk modal tambah produk
    $allProducts = \App\Models\Product::with('unit')
        ->get()
        ->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'unit_name' => $p->unit?->name ?? '-',
            ];
        });
@endphp

<div class="erp-wrapper" x-data="{
    state: $wire.entangle('{{ $getStatePath() }}'),
    search: '',
    showModal: false,
    modalSearch: '',
    allProducts: {{ json_encode($allProducts) }},

    // Logika Hitung Kerusakan & Total Harga
    calculatewaster(key) {
        // Pastikan jika kosong jadi 0
        let qty = parseFloat(this.state[key].waster_qty) || 0;
        let price = parseFloat(this.state[key].waster_price) || 0;

        // Update state dengan angka murni
        this.state[key].waster_qty = qty;
        this.state[key].waster_price = price;
        this.state[key].waster_total_price = parseFloat((qty * price).toFixed(2));

        this.updateDifference(key);
    },

    // Rumus Selisih: Fisik - Sistem - Rusak (Agar Tersimpan ke DB)
    updateDifference(key) {
        // 1. Ambil nilai dan pastikan dikonversi ke Float murni
        let system = parseFloat(this.state[key].system_stock) || 0;
        let physical = parseFloat(this.state[key].physical_stock) || 0;
        let waster = parseFloat(this.state[key].waster_qty) || 0;

        // 2. Hitung selisih
        let diff = physical - system - waster;

        // 3. Simpan kembali ke state sebagai NUMBER (bukan string)
        // Menggunakan Number() setelah toFixed memastikan hasilnya tetap desimal tapi tipenya angka
        this.state[key].difference = Number(diff.toFixed(2));
        this.state[key].physical_stock = Number(physical.toFixed(2));
        this.state[key].waster_qty = Number(waster.toFixed(5)); // Qty biasanya butuh lebih banyak desimal
    },

    get filteredItems() {
        if (!this.state || typeof this.state !== 'object') return [];
        let items = Object.entries(this.state).map(([key, val]) => ({ key, ...val }));
        if (this.search.trim() !== '') {
            items = items.filter(i => i.product_name.toLowerCase().includes(this.search.toLowerCase()));
        }
        return items;
    },

    get availableProducts() {
        let existingIds = Object.values(this.state).map(i => i.product_id);
        return this.allProducts.filter(p =>
            !existingIds.includes(p.id) &&
            p.name.toLowerCase().includes(this.modalSearch.toLowerCase())
        );
    },

    addProduct(product) {
        const key = 'new_' + Date.now();

        // Gunakan Spread Operator (...) untuk membuat instance object baru
        // Ini memastikan Alpine.js mendeteksi adanya perubahan pada variabel 'state'
        this.state = {
            ...this.state,
            [key]: {
                product_id: product.id,
                product_name: product.name,
                unit_name: product.unit_name,
                system_stock: 0,
                physical_stock: 0,
                waster_qty: 0,
                waster_price: 0,
                waster_total_price: 0,
                difference: 0
            }
        };

        this.showModal = false;
        this.modalSearch = '';
    },
}">

    <div class="header-toolbar">
        <div class="search-container">
            <input type="text" x-model="search" placeholder="Cari di list table..." class="search-input">
        </div>
        <button type="button" @click="showModal = true" class="add-btn" {{ $isDisabled ? 'disabled' : '' }}>
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Produk (CTRL + B)
        </button>
    </div>

    <div class="table-responsive-scroll">
        <table class="erp-table">
            <thead>
                <tr>
                    <th class="text-left">Nama Produk</th>
                    <th class="text-left">Satuan</th>
                    <th class="text-center">Sistem</th>
                    <th class="text-center">Fisik</th>
                    <th class="text-center" style="color: #2563eb;">Rusak (Qty)</th>
                    <th class="text-center" style="color: #2563eb;">Harga Rusak</th>
                    <th class="text-center" style="color: #2563eb;">Total Rusak</th>
                    <th class="text-center">Selisih Murni</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="item in filteredItems" :key="item.key">
                    <tr>
                        <td class="text-left">
                            <div class="item-box">
                                <span class="item-name" x-text="item.product_name"></span>
                            </div>
                        </td>
                        <td class="text-left">
                            <span x-text="item.unit_name" style="font-size: 12px; color: #64748b;"></span>
                        </td>
                        <td class="text-center">
                            <span class="qty-badge-system" x-text="item.system_stock"></span>
                        </td>
                        <td class="text-center">
                            <input type="number" step="0.01" inputmode="decimal"
                                x-model.number="state[item.key].physical_stock" @input="updateDifference(item.key)"
                                @focus="$event.target.select()" class="input-field input-focus-green"
                                {{ $isDisabled ? 'disabled' : '' }}>
                        </td>
                        <td class="text-center">
                            <input type="number" step="0.00001" inputmode="decimal"
                                x-model.number="state[item.key].waster_qty" @input="calculatewaster(item.key)"
                                @focus="$event.target.select()" class="input-field-blue"
                                {{ $isDisabled ? 'disabled' : '' }}>
                        </td>
                        <td class="text-center">
                            <input type="number" step="any" x-model="state[item.key].waster_price"
                                @input="calculatewaster(item.key)" @focus="$event.target.select()"
                                class="input-field-blue" style="width: 100px;" {{ $isDisabled ? 'disabled' : '' }}>
                        </td>
                        <td class="text-center">
                            <span style="font-weight: 800; color: #1e40af; font-size: 13px;">
                                Rp <span
                                    x-text="new Intl.NumberFormat('id-ID').format(state[item.key].waster_total_price || 0)"></span>
                            </span>
                        </td>
                        <td class="text-center">
                            <span
                                :class="{
                                    'diff-badge': true,
                                    'diff-positive': item.difference > 0,
                                    'diff-negative': item.difference < 0,
                                    'diff-neutral': item.difference == 0
                                }"
                                x-text="(item.difference > 0 ? '+' : '') + item.difference"></span>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div class="custom-modal-overlay" x-show="showModal" x-cloak x-transition @click.self="showModal = false">
        <div class="custom-modal-content">
            <div class="modal-header">
                <h3 style="font-weight: 800; color: #1e293b; margin: 0;">Pilih Produk Baru</h3>
                <button type="button" @click="showModal = false" class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="text" x-model="modalSearch" placeholder="Cari nama produk di database..."
                    class="search-input modal-search" style="margin-bottom: 15px;">
                <div class="product-list-scroll">
                    <template x-for="p in availableProducts" :key="p.id">
                        <div class="product-item" @click="addProduct(p)">
                            <div class="p-info">
                                <span class="p-name" x-text="p.name" style="font-weight: bold; display: block;"></span>
                                <span class="p-id" x-text="'Unit: ' + p.unit_name"
                                    style="font-size: 11px; color: #94a3b8;"></span>
                            </div>
                            <span class="p-add-icon">+</span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Container Utama */
    .erp-wrapper {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        background: white;
    }

    /* Header & Search */
    .header-toolbar {
        display: flex;
        padding: 12px;
        gap: 10px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        align-items: center;
    }

    .search-container {
        flex-grow: 1;
    }

    .search-input {
        width: 100%;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        outline: none;
        transition: 0.2s;
    }

    .search-input:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.1);
    }

    /* Tombol Tambah */
    .add-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #22c55e;
        color: white;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: 0.2s;
    }

    .add-btn:hover {
        background: #16a34a;
    }

    .add-btn:disabled {
        background: #cbd5e1;
        cursor: not-allowed;
    }

    /* Tabel Styling */
    .table-responsive-scroll {
        max-height: 500px;
        overflow-y: auto;
    }

    .erp-table {
        width: 100%;
        border-collapse: collapse;
    }

    .erp-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f0fdf4;
        border-bottom: 2px solid #22c55e;
        padding: 12px;
        font-size: 11px;
        font-weight: 800;
        color: #166534;
        text-transform: uppercase;
    }

    .erp-table td {
        padding: 10px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    /* Badge & Input */
    .qty-badge-system {
        font-weight: 700;
        color: #475569;
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 13px;
    }

    .input-field {
        width: 80px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        text-align: center;
        font-weight: 700;
    }

    .input-field-blue {
        width: 80px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid #dbeafe;
        background: #eff6ff;
        color: #2563eb;
        font-weight: bold;
        text-align: center;
    }

    .input-focus-green:focus {
        border-color: #22c55e;
        outline: none;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
    }

    /* Selisih Badge */
    .diff-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 800;
        font-size: 12px;
    }

    .diff-positive {
        background: #dcfce7;
        color: #15803d;
    }

    .diff-negative {
        background: #fee2e2;
        color: #b91c1c;
    }

    .diff-neutral {
        background: #f1f5f9;
        color: #64748b;
    }

    /* --- MODAL STYLE (YANG HILANG) --- */
    .custom-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
        padding: 20px;
    }

    .custom-modal-content {
        background: white;
        width: 100%;
        max-width: 550px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: modalFadeIn 0.3s ease-out;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .close-modal {
        font-size: 28px;
        color: #94a3b8;
        cursor: pointer;
        border: none;
        background: none;
        line-height: 1;
    }

    .modal-body {
        padding: 20px;
    }

    .product-list-scroll {
        max-height: 350px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .product-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        border-radius: 10px;
        cursor: pointer;
        margin-bottom: 8px;
        border: 1px solid #f1f5f9;
        transition: 0.2s;
    }

    .product-item:hover {
        background: #f0fdf4;
        border-color: #22c55e;
        transform: scale(1.01);
    }

    .p-add-icon {
        background: #22c55e;
        color: white;
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: bold;
        font-size: 18px;
    }

    [x-cloak] {
        display: none !important;
    }
</style>

<script>
    document.addEventListener('keydown', function(e) {
        // Cek apakah yang ditekan Ctrl + B
        if (e.ctrlKey && e.key.toLowerCase() === 'b') {
            e.preventDefault(); // Stop fungsi bold bawaan browser

            // Cari tombol berdasarkan class, lalu klik secara programmatik
            const btn = document.querySelector('.add-btn');
            if (btn && !btn.disabled) {
                btn.click();
            }
        }
    });
</script>
