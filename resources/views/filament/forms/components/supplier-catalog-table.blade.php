@php
    // Gunakan try-catch sederhana untuk memastikan query tidak crash
    try {
        $allProducts = \App\Models\Product::select('id', 'name', 'code', 'hpp')->get();
    } catch (\Exception $e) {
        $allProducts = collect();
    }
@endphp

<div class="erp-container" x-data="{
    state: $wire.entangle('{{ $getStatePath() }}') || [],
    showModal: false,
    modalSearch: '',
    selectedInModal: [],
    // Pastikan data ter-encode dengan benar
    allProducts: @js($allProducts),

    get filteredProducts() {
        // Pastikan state adalah array
        let currentState = Array.isArray(this.state) ? this.state : [];
        let existingIds = currentState.map(item => String(item.product_id));

        let filtered = this.allProducts.filter(p => {
            let isNotAdded = !existingIds.includes(String(p.id));
            let matchesSearch = p.name.toLowerCase().includes(this.modalSearch.toLowerCase()) ||
                (p.code && String(p.code).toLowerCase().includes(this.modalSearch.toLowerCase()));
            return isNotAdded && matchesSearch;
        });

        console.log('Filtered Products:', filtered); // Cek di F12 / Console browser
        return filtered;
    },

    confirmBulkSelect() {
        if (!Array.isArray(this.state)) this.state = [];

        this.selectedInModal.forEach(id => {
            let p = this.allProducts.find(prod => prod.id == id);
            if (p) {
                this.state.push({
                    product_id: p.id,
                    product_name: p.name,
                    sku_supplier: p.code || '',
                    harga_beli_khusus: p.hpp || 0,
                    branch_id: 1
                });
            }
        });
        this.showModal = false;
        this.selectedInModal = [];
        this.modalSearch = '';
    }
}" x-init="console.log('Initial Products:', allProducts)">
    <div class="erp-header">
        <div class="header-title">
            <div class="blue-dot"></div>
            Katalog Produk Supplier
        </div>
        <button type="button" @click="showModal = true" class="btn-primary-blue">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Pilih Produk Master
        </button>
    </div>

    <div class="erp-table-wrapper">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Informasi Produk</th>
                    <th style="width: 180px">SKU Supplier</th>
                    <th style="width: 220px">Harga Kontrak</th>
                    <th style="width: 50px"></th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in state" :key="index">
                    <tr>
                        <td>
                            <div class="p-info">
                                <span class="p-name" x-text="item.product_name"></span>
                                <span class="p-sku" x-text="'ID: #' + item.product_id"></span>
                            </div>
                        </td>
                        <td><input type="text" x-model="item.sku_supplier" class="erp-input"></td>
                        <td>
                            <div class="currency-group">
                                <span class="currency-label">Rp</span>
                                <input type="number" x-model="item.harga_beli_khusus" class="erp-input text-right">
                            </div>
                        </td>
                        <td>
                            <button type="button" @click="state.splice(index, 1)" class="btn-delete">&times;</button>
                        </td>
                    </tr>
                </template>
                <template x-if="!state || state.length === 0">
                    <tr>
                        <td colspan="4" class="empty-row">Belum ada katalog. Silakan pilih produk.</td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div class="erp-modal-overlay" x-show="showModal" x-cloak @click.self="showModal = false">
        <div class="erp-modal-content" x-show="showModal">
            <div class="modal-header-blue">
                <div>
                    <h4>Pilih Master Produk</h4>
                    <p x-text="filteredProducts.length + ' Produk Tersedia'"></p>
                </div>
                <button type="button" @click="showModal = false" class="close-modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="text" x-ref="searchInput" x-model="modalSearch"
                    placeholder="Ketik nama produk untuk mencari..." class="search-input">

                <div class="list-container">
                    <template x-for="p in filteredProducts" :key="p.id">
                        <label class="product-item">
                            <input type="checkbox" :value="p.id" x-model="selectedInModal"
                                class="erp-checkbox">
                            <div class="item-detail">
                                <span class="item-name" x-text="p.name"></span>
                                <span class="item-code" x-text="'Code: ' + (p.code || '-')"></span>
                            </div>
                        </label>
                    </template>

                    <template x-if="filteredProducts.length === 0">
                        <div class="no-data">Produk tidak ditemukan...</div>
                    </template>
                </div>
            </div>

            <div class="modal-footer">
                <span class="selected-count" x-text="selectedInModal.length + ' dipilih'"></span>
                <div class="footer-btns">
                    <button type="button" @click="showModal = false" class="btn-cancel">Batal</button>
                    <button type="button" @click="confirmBulkSelect" class="btn-confirm-blue"
                        :disabled="selectedInModal.length === 0">
                        Tambahkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* CSS SAMA SEPERTI SEBELUMNYA DENGAN TEMA BIRU */
        .erp-container {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .erp-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: #1e293b;
        }

        .blue-dot {
            width: 8px;
            height: 8px;
            background: #3b82f6;
            border-radius: 50%;
        }

        .btn-primary-blue {
            background: #2563eb;
            color: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table th {
            background: #f1f5f9;
            padding: 12px 20px;
            text-align: left;
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
        }

        .modern-table td {
            padding: 12px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .erp-input {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 13px;
            outline: none;
        }

        .currency-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .currency-label {
            position: absolute;
            left: 12px;
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
        }

        .currency-group input {
            padding-left: 35px;
        }

        .text-right {
            text-align: right;
        }

        .btn-delete {
            color: #94a3b8;
            font-size: 24px;
            border: none;
            background: none;
            cursor: pointer;
        }

        .btn-delete:hover {
            color: #ef4444;
        }

        .empty-row {
            padding: 40px !important;
            text-align: center;
            color: #94a3b8;
            font-style: italic;
        }

        .erp-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.7);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .erp-modal-content {
            background: #fff;
            width: 550px;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 80vh;
        }

        .modal-header-blue {
            background: #1e293b;
            padding: 15px 25px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header-blue h4 {
            margin: 0;
            font-size: 16px;
        }

        .modal-header-blue p {
            margin: 0;
            font-size: 11px;
            color: #94a3b8;
        }

        .close-modal {
            font-size: 24px;
            color: #fff;
            background: none;
            border: none;
            cursor: pointer;
        }

        .modal-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .search-input {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 15px;
            outline: none;
        }

        .list-container {
            flex-grow: 1;
            overflow-y: auto;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
        }

        .product-item:hover {
            background: #eff6ff;
        }

        .erp-checkbox {
            width: 18px;
            height: 18px;
            accent-color: #2563eb;
        }

        .item-detail {
            margin-left: 12px;
        }

        .item-name {
            display: block;
            font-weight: 700;
            color: #1e293b;
            font-size: 13px;
        }

        .item-code {
            font-size: 11px;
            color: #64748b;
        }

        .modal-footer {
            padding: 15px 25px;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .selected-count {
            color: #2563eb;
            font-weight: 700;
            font-size: 13px;
        }

        .btn-confirm-blue {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-confirm-blue:disabled {
            background: #cbd5e1;
        }

        .btn-cancel {
            background: none;
            border: none;
            color: #64748b;
            font-weight: 600;
            cursor: pointer;
        }

        .no-data {
            padding: 30px;
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
        }

        [x-cloak] {
            display: none !important;
        }

        .w-4 {
            width: 1rem;
        }

        .h-4 {
            height: 1rem;
        }
    </style>
</div>
