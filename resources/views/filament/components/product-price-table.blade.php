<div x-data="{
    openModal: false,
    productId: '',
    price: 0,
    editIndex: null,
    prices: @entangle($getStatePath()),

    // Ambil data produk lengkap
    productList: {{ json_encode(
        \App\Models\Product::with('unit:id,name')->get()->map(
                fn($p) => [
                    'id' => $p->id,
                    'display' => $p->name . ' - ' . ($p->unit?->name ?? '-'),
                    'hpp' => $p->hpp,
                ],
            )->toArray(),
    ) }},

    init() {
        if (!Array.isArray(this.prices)) this.prices = [];
    },

    updateDefaultPrice() {
        // Hanya auto-fill jika sedang tambah baru (bukan edit)
        if (this.editIndex !== null) return;

        const prod = this.productList.find(p => p.id == this.productId);
        this.price = prod ? prod.hpp : 0;
    },

    editPrice(index) {
        this.editIndex = index;
        const item = this.prices[index];
        this.productId = item.product_id;
        this.price = item.special_price;
        this.openModal = true;
    },

    savePrice() {
        if (!this.productId) return;

        const prod = this.productList.find(p => p.id == this.productId);

        if (this.editIndex !== null) {
            // Logika Update
            this.prices[this.editIndex].product_id = this.productId;
            this.prices[this.editIndex].product_display_name = prod.display;
            this.prices[this.editIndex].special_price = parseFloat(this.price);
        } else {
            // Logika Tambah Baru
            this.prices.push({
                product_id: this.productId,
                product_display_name: prod.display,
                special_price: parseFloat(this.price)
            });
        }

        this.closeAndReset();
    },

    deletePrice(index) {
        if(confirm('Hapus harga khusus ini?')) {
            this.prices.splice(index, 1);
        }
    },

    closeAndReset() {
        this.openModal = false;
        this.productId = '';
        this.price = 0;
        this.editIndex = null;
    },

    get filteredProductList() {
        const existingIds = this.prices.map(p => p.product_id.toString());
        
        return this.productList.filter(p => {
            // Jika sedang edit, ID produk yang sedang diedit harus tetap muncul
            if (this.editIndex !== null && p.id == this.prices[this.editIndex].product_id) {
                return true;
            }
            // Sisanya, filter yang belum ada di list
            return !existingIds.includes(p.id.toString());
        });
    },

    formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    },
}" class="modern-blue-container">

    <div class="table-card">
        <table class="pure-table">
            <thead>
                <tr>
                    <th><span style="margin-right: 8px;">📦</span> Produk & Satuan</th>
                    <th><span style="margin-right: 8px;">💰</span> Harga Spesial</th>
                    <th style="width: 100px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in prices" :key="index">
                    <tr>
                        <td x-text="item.product_display_name"></td>
                        <td style="font-weight: 600; color: #0f172a;">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(item.special_price)"></span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <button type="button" @click="editPrice(index)" class="btn-edit" title="Edit">
                                    ✏️
                                </button>
                                <button type="button" @click="deletePrice(index)" class="btn-delete" title="Hapus">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

                <template x-if="prices.length === 0">
                    <tr>
                        <td colspan="3" class="empty-text">Belum ada harga khusus untuk customer ini</td>
                    </tr>
                </template>
            </tbody>
        </table>

        <div class="table-footer">
            <button type="button" @click="openModal = true" class="btn-add">
                + Atur Harga Produk
            </button>
        </div>
    </div>

    <template x-teleport="body">
        <div x-show="openModal" x-transition.opacity class="modal-overlay" style="display: none;">
            <div @click.away="closeAndReset()" x-show="openModal" x-transition.scale.95 class="modal-content">

                <div class="modal-header">
                    <h3 x-text="editIndex !== null ? 'Edit Harga Spesial' : 'Atur Harga Spesial'"></h3>
                    <button type="button" @click="closeAndReset()" class="close-x">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Produk & Satuan</label>
                        <select x-model="productId" @change="updateDefaultPrice()" class="custom-select" :disabled="editIndex !== null">
                            <option value="">-- Pilih Produk --</option>
                            <template x-for="p in filteredProductList" :key="p.id">
                                <option :value="p.id" x-text="p.display"></option>
                            </template>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Harga Khusus (Rp)</label>
                        <input type="number" x-model="price" placeholder="Masukkan harga..." class="custom-input"
                            @focus="$event.target.select()">
                        <template x-if="price > 0">
                            <div style="margin-top: 8px; font-size: 14px; color: #2563eb; font-weight: 600;">
                                Terformat: <span x-text="formatRupiah(price)"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" @click="closeAndReset()" class="btn-cancel">Batal</button>
                    <button type="button" @click="savePrice()" class="btn-save" x-text="editIndex !== null ? 'Simpan Perubahan' : 'Simpan Harga'"></button>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
    /* Gunakan style yang sama dengan sebelumnya, tambahkan .btn-edit */
    :root {
        --blue-main: #2563eb;
        --blue-dark: #1e40af;
        --blue-light: #eff6ff;
        --gray-border: #e2e8f0;
    }

    .table-card { border: 1px solid var(--gray-border); border-radius: 12px; overflow: hidden; background: white; }
    .pure-table { width: 100%; border-collapse: collapse; }
    .pure-table th { background: #f8fafc; padding: 14px 16px; text-align: left; font-size: 11px; color: var(--blue-dark); text-transform: uppercase; border-bottom: 2px solid var(--blue-light); }
    .pure-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #475569; }
    
    .btn-add { background: var(--blue-main); color: white; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; }
    
    .btn-edit, .btn-delete { border: none; padding: 8px; border-radius: 8px; cursor: pointer; transition: 0.2s; }
    .btn-edit { background: #e0f2fe; color: #0369a1; }
    .btn-edit:hover { background: #bae6fd; transform: translateY(-2px); }
    .btn-delete { background: #fee2e2; color: #b91c1c; }
    .btn-delete:hover { background: #fecaca; transform: translateY(-2px); }

    .modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); display: flex; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(4px); }
    .modal-content { background: white; width: 100%; max-width: 420px; border-radius: 20px; overflow: hidden; }
    .modal-header { padding: 20px; border-bottom: 1px solid var(--gray-border); display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 24px; }
    .form-group { margin-bottom: 20px; }
    .custom-select, .custom-input { width: 100%; padding: 12px; border: 1.5px solid #e2e8f0; border-radius: 10px; }
    .modal-footer { padding: 16px 24px; background: #f8fafc; display: flex; justify-content: flex-end; gap: 12px; }
    .btn-save { background: var(--blue-main); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; }
    .btn-cancel { background: white; border: 1px solid #e2e8f0; padding: 10px 18px; border-radius: 8px; color: #64748b; cursor: pointer; }
</style>