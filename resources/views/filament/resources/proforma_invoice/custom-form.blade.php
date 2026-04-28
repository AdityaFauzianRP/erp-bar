<style>
    /* Container & General */
    .pi-container {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1e293b;
        background-color: #f8fafc;
        padding: 24px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        position: relative;
    }

    /* Header Card */
    .pi-card {
        background: white;
        padding: 24px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .pi-header-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
    }

    .form-input {
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: all 0.2s;
        width: 100%;
    }

    .form-input:focus:not(:disabled) {
        border-color: #5c7cfa;
        box-shadow: 0 0 0 3px rgba(92, 124, 250, 0.15);
    }

    /* Due Date Quick Selection */
    .due-section {
        grid-column: 1 / -1;
        border-top: 1px solid #f1f5f9;
        padding-top: 20px;
        margin-top: 10px;
    }

    .due-options {
        display: flex;
        gap: 12px;
        margin: 12px 0;
        flex-wrap: wrap;
    }

    .due-btn {
        padding: 10px 20px;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .due-btn:hover {
        border-color: #5c7cfa;
        color: #5c7cfa;
    }

    .due-btn.active {
        background-color: #5c7cfa;
        border-color: #5c7cfa;
        color: white;
    }

    /* Table Design */
    .pi-table-wrapper {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-top: 20px;
    }

    .pi-table {
        width: 100%;
        border-collapse: collapse;
    }

    .pi-table th {
        background-color: #f8fafc;
        padding: 16px;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        border-bottom: 2px solid #f1f5f9;
    }

    .pi-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .btn-add {
        background-color: #2563eb;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .pi-footer {
        margin-top: 30px;
        background-color: #0f172a;
        color: white;
        padding: 32px;
        border-radius: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
    }

    .total-amount {
        font-size: 36px;
        font-weight: 800;
        color: #38bdf8;
    }

    .notes-area {
        background: #1e293b;
        border: 1px solid #334155;
        color: #e2e8f0;
        padding: 16px;
        border-radius: 12px;
        width: 100%;
        max-width: 450px;
    }

    .btn-save {
        background-color: #10b981;
        color: white;
        padding: 14px 40px;
        border: none;
        border-radius: 10px;
        font-weight: 800;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-save:hover {
        background-color: #059669;
        transform: translateY(-1px);
    }

    .btn-cancel {
        padding: 14px 30px;
        background: #94a3b8;
        color: white;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
    }

    /* CUSTOM MODAL STYLES */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-box {
        background: white;
        width: 90%;
        max-width: 420px;
        padding: 32px;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 32px;
    }

    .icon-confirm {
        background: #eff6ff;
        color: #3b82f6;
    }

    .icon-success {
        background: #ecfdf5;
        color: #10b981;
    }

    .icon-error {
        background: #fef2f2;
        color: #ef4444;
    }

    .modal-title {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .modal-msg {
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 28px;
    }

    .modal-btns {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<div x-data="{
    state: @entangle($getStatePath()),
    productList: [],
    brandList: [],
    dueOption: 'custom',
    formattedDate: '',

    /* Modal States */
    showModal: false,
    modalType: 'confirm',
    modalTitle: '',
    modalMessage: '',
    isLoading: false,

    formatDisplay(val) {
        if (!val) return '0';
        return new Intl.NumberFormat('id-ID').format(val);
    },

    selectAll(e) {
        e.target.select();
    },

    async init() {
        if (!this.state) {
            this.state = {
                items: [],
                customer_induk_id: null,
                customer_brand_id: null,
                number: '',
                total_amount: 0,
                ppn_percent: 0,
                ppn_amount: 0,
                grand_total: 0,
                date: new Date().toISOString().split('T')[0]
            };
        }
        if (!this.state.items) this.state.items = [];

        if (this.state.customer_induk_id) {
            await Promise.all([this.loadBrands(), this.loadProducts()]);
        }

        this.$watch('state.date', async (val) => {
            if (val) {
                const newNumber = await this.$wire.generatePINumber(val);
                this.state.number = newNumber;
                if (this.dueOption !== 'custom') this.calculateDue(this.dueOption);
            }
        });

        if (this.state.date && !this.state.number) {
            this.$wire.generatePINumber(this.state.date).then(res => this.state.number = res);
        }
        this.calculateGrandTotal();
    },

    calculateDue(days) {
        this.dueOption = days;
        if (days === 'custom') return;
        let baseStr = (this.state && this.state.date) ? this.state.date : new Date().toISOString().split('T')[0];
        const parts = baseStr.split('-');
        let base = new Date(parts[0], parts[1] - 1, parts[2]);
        let result = new Date(base);
        result.setDate(result.getDate() + parseInt(days));
        const y = result.getFullYear();
        const m = String(result.getMonth() + 1).padStart(2, '0');
        const d = String(result.getDate()).padStart(2, '0');
        this.state.due_date = `${y}-${m}-${d}`;
        this.formattedDate = `${d}/${m}/${y}`;
    },

    async loadProducts() {
        if (!this.state?.customer_induk_id) { this.productList = []; return; }
        try {
            const res = await fetch('/api/customer-products/' + this.state.customer_induk_id);
            this.productList = await res.json();
        } catch (e) { console.error(e); }
    },

    async loadBrands() {
        if (!this.state?.customer_induk_id) { this.brandList = []; return; }
        this.loadProducts();
        try {
            const res = await fetch('/api/customer-brands/' + this.state.customer_induk_id);
            this.brandList = await res.json();
            if (this.brandList.length === 1) this.state.customer_brand_id = this.brandList[0].id;
        } catch (e) { console.error(e); }
    },

    updateProduct(index) {
        let item = this.state.items[index];
        if (!item.product_id) return;

        const prod = this.productList.find(p => p.id == item.product_id);

        if (prod) {
            item.unit_price = prod.price || 0;
            item.hpp_price = prod.hpp || 0;
        } else {
            item.unit_price = 0;
            item.hpp_price = 0;
        }

        this.updateSubtotal(index);
    },

    updateSubtotal(index) {
        this.state.items[index].subtotal = (this.state.items[index].qty || 0) * (this.state.items[index].unit_price || 0);
        this.calculateGrandTotal();
    },

    calculateGrandTotal() {
        const totalDPP = this.state.items.reduce((sum, item) => sum + parseFloat(item.subtotal || 0), 0);
        this.state.total_amount = totalDPP;
        const taxPercent = parseFloat(this.state.ppn_percent || 0);
        this.state.ppn_amount = (totalDPP * taxPercent) / 100;
        this.state.grand_total = totalDPP + this.state.ppn_amount;
    },

    /* CUSTOM MODAL HANDLERS */
    triggerConfirm() {
        if (!this.state.customer_induk_id) return this.openAlert('Peringatan', 'Pilih Customer terlebih dahulu!', 'error');
        if (this.state.items.length === 0) return this.openAlert('Item Kosong', 'Tambahkan minimal 1 item!', 'error');

        this.modalType = 'confirm';
        this.modalTitle = 'Simpan Data?';
        this.modalMessage = 'Pastikan semua data Proforma Invoice sudah sesuai sebelum disimpan ke sistem.';
        this.showModal = true;
    },

    openAlert(title, msg, type) {
        this.modalType = type;
        this.modalTitle = title;
        this.modalMessage = msg;
        this.showModal = true;
    },

    async processStore() {
        this.isLoading = true;
        const token = document.querySelector('input[name=_token]')?.value || document.querySelector('meta[name=csrf-token]')?.content;


        const hasEmptyProduct = this.state.items.some(item => !item.product_id);

        if (hasEmptyProduct) {
            this.openAlert('Gagal Simpan', 'Ada produk yang belum dipilih pada baris item.', 'error');
            this.isLoading = false; // Pastikan loading berhenti di sini
            return; // Hentikan proses, jangan lanjut ke fetch
        }
        try {
            const response = await fetch('/api/proforma-invoices/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(this.state)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                this.modalType = 'success';
                this.modalTitle = 'Berhasil!';
                this.modalMessage = 'Proforma Invoice berhasil diterbitkan.';

                // Tunggu sebentar lalu redirect
                setTimeout(() => window.location.href = '/erp/proforma-invoices', 1200);
            } else {
                // Jika validasi backend gagal (422) atau error lainnya
                this.isLoading = false;
                this.openAlert('Gagal Simpan', result.message || 'Terjadi kesalahan server', 'error');
            }
        } catch (e) {
            this.isLoading = false;
            this.openAlert('Koneksi Error', 'Gagal terhubung ke server.', 'error');
        } finally {
            // PERBAIKAN: Selalu matikan loading di akhir jika tidak sukses (redirect)
            // Jika sukses, biarkan saja loading tetap true sampai halaman berpindah
            if (this.modalType !== 'success') {
                this.isLoading = false;
            }
        }
    },

    calculateMargin(item) {
        if (!item.unit_price || !item.hpp_price) return 0;
        const margin = ((item.unit_price - item.hpp_price) / item.unit_price) * 100;
        return margin.toFixed(2);
    }
}" class="pi-container">

    <template x-if="showModal">
        <div class="modal-overlay" @click.self="if(!isLoading) showModal = false">
            <div class="modal-box" x-transition>
                <div class="modal-icon" :class="'icon-' + modalType">
                    <template x-if="modalType === 'confirm'"><span>❓</span></template>
                    <template x-if="modalType === 'success'"><span>✅</span></template>
                    <template x-if="modalType === 'error'"><span>❌</span></template>
                </div>
                <h3 class="modal-title" x-text="modalTitle"></h3>
                <p class="modal-msg" x-text="modalMessage"></p>
                <div class="modal-btns">
                    <template x-if="modalType === 'confirm' && !isLoading">
                        <button @click="showModal = false" class="btn-cancel" style="padding: 10px 20px;">Batal</button>
                    </template>
                    <button @click="modalType === 'confirm' ? processStore() : showModal = false" class="btn-save"
                        :disabled="isLoading" style="padding: 10px 24px;">
                        <span x-show="isLoading" class="spinner"></span>
                        <span x-show="!isLoading" x-text="modalType === 'confirm' ? 'Ya, Simpan' : 'Tutup'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <div class="pi-card">
        <div class="pi-header-grid">
            <div class="form-group">
                <label>Nomor PI</label>
                <input type="text" x-model="state.number" readonly class="form-input"
                    style="background: #f1f5f9; font-weight: bold;">
            </div>
            <div class="form-group">
                <label>Nomor PO Customer</label>
                <input type="text" x-model="state.po_number" class="form-input" placeholder="Input nomor PO...">
            </div>
            <div class="form-group">
                <label>Customer Induk</label>
                <select x-model.number="state.customer_induk_id" @change="loadBrands()" class="form-input">
                    <option value="">Pilih Customer</option>
                    @foreach (\App\Models\CustomerInduk::all() as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Brand / Cabang</label>
                <select x-model.number="state.customer_brand_id" class="form-input">
                    <option value="">Pilih Cabang</option>
                    <template x-for="brand in brandList" :key="brand.id">
                        <option :value="brand.id" x-text="`${brand.name} - ${brand.kota_cabang}`"></option>
                    </template>
                </select>
            </div>
            <div class="form-group">
                <label>Tanggal SO</label>
                <input type="date" x-model="state.date" class="form-input">
            </div>
            <div class="form-group">
                <label>Deadline Pengiriman</label>
                <input type="date" x-model="state.delivery_deadline" class="form-input">
            </div>

            <div class="due-section">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 700;">
                    Jatuh Tempo Pembayaran <span style="color:red">*</span>
                </label>
                <div class="due-options">
                    <button type="button" class="due-btn" :class="dueOption == 7 ? 'active' : ''"
                        @click="calculateDue(7)">7 Hari</button>
                    <button type="button" class="due-btn" :class="dueOption == 14 ? 'active' : ''"
                        @click="calculateDue(14)">14 Hari</button>
                    <button type="button" class="due-btn" :class="dueOption == 30 ? 'active' : ''"
                        @click="calculateDue(30)">30 Hari</button>
                    <button type="button" class="due-btn" :class="dueOption == 'custom' ? 'active' : ''"
                        @click="dueOption = 'custom'; formattedDate = ''">Custom</button>
                </div>
                <div style="margin-top: 15px;">
                    <template x-if="dueOption !== 'custom'">
                        <div class="form-group">
                            <input type="text" :value="formattedDate" readonly class="form-input"
                                style="background: #f1f5f9; border-color: #5c7cfa; font-weight: bold;">
                        </div>
                    </template>
                    <template x-if="dueOption === 'custom'">
                        <div class="form-group">
                            <input type="date" x-model="state.due_date" class="form-input"
                                style="border-color: #5c7cfa;">
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 18px; font-weight: 800; color: #0f172a;">Daftar Barang</h3>
        <button type="button" class="btn-add"
            @click="state.items.push({product_id:'', qty:1, unit_price:0, hpp_price:0, subtotal:0}); calculateGrandTotal();">
            + Tambah Item
        </button>
    </div>

    <div class="pi-table-wrapper">
        <table class="pi-table">
            <thead>
                <tr>
                    <th>Informasi Produk</th>
                    <th style="text-align: center;">Satuan</th>
                    <th style="width: 120px;">Qty</th>
                    <th>Harga Jual Customer</th>
                    <th>HPP</th>
                    <th style="width: 120px;">Margin (%)</th>
                    <th>Subtotal</th>
                    <th style="width: 50px;"></th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in state.items" :key="index">
                    <tr>
                        <td>
                            <select x-model.number="item.product_id" @change="updateProduct(index)" class="form-input">
                                <option value="">Pilih Produk</option>
                                <template x-for="p in productList" :key="p.id">
                                    <option :value="p.id" x-text="p.name"></option>
                                </template>
                            </select>
                        </td>
                        <td style="text-align: center;">
                            <span x-text="productList.find(p => p.id == item.product_id)?.unit || '-'"></span>
                        </td>
                        <td>
                            <input type="number" x-model.number="item.qty" @input="updateSubtotal(index)"
                                @focus="selectAll($event)" class="form-input">
                        </td>
                        <td>
                            <input type="number" x-model.number="item.unit_price" @input="updateSubtotal(index)"
                                @focus="selectAll($event)" class="form-input">
                        </td>
                        <td>
                            <input type="number" x-model.number="item.hpp_price" @input="updateSubtotal(index)"
                                @focus="selectAll($event)" class="form-input">
                        </td>
                        {{-- Tambahkan Presentasi Margin penjuaan --}}
                        <td>
                            <span x-text="formatDisplay(calculateMargin(item))">%</span>
                        </td>
                        <td style="font-weight: 700;">
                            Rp <span x-text="formatDisplay(item.subtotal)"></span>
                        </td>
                        <td>
                            <button type="button" @click="state.items.splice(index, 1); calculateGrandTotal();"
                                style="color: #ef4444; border: none; background: none; cursor: pointer;">🗑️</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div class="pi-footer">
        <div class="tax-box">
            <div
                style="display: flex; align-items: center; gap: 10px; background: #1e293b; padding: 8px 12px; border-radius: 8px; border: 1px solid #334155;">
                <label style="font-size: 11px; font-weight: 700; color: #94a3b8;">PPN (%)</label>
                <input type="number" x-model.number="state.ppn_percent" @input="calculateGrandTotal()"
                    @focus="selectAll($event)"
                    style="width: 60px; background: #0f172a; border: 1px solid #475569; color: #38bdf8; padding: 4px 8px; border-radius: 4px; text-align: center; font-weight: bold;">
                <div style="font-size: 13px; color: #94a3b8; margin-left: 10px;">
                    Pajak: Rp <span x-text="formatDisplay(state.ppn_amount)"></span>
                </div>
            </div>

            <div style="display: flex; gap: 30px; align-items: flex-end; margin-top: 10px;">
                <div>
                    <div style="color: #94a3b8; font-size: 11px; font-weight: 700;">TOTAL DPP</div>
                    <div style="font-size: 20px; font-weight: 700;">Rp <span
                            x-text="formatDisplay(state.total_amount)"></span></div>
                </div>
                <div>
                    <div style="color: #38bdf8; font-size: 11px; font-weight: 700;">GRAND TOTAL</div>
                    <div class="total-amount" style="line-height: 1;">Rp <span
                            x-text="formatDisplay(state.grand_total)"></span></div>
                </div>
            </div>
        </div>
        <textarea x-model="state.notes" class="notes-area" rows="3" placeholder="Tambahkan catatan di sini..."></textarea>
    </div>

    <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
        <a href="/erp/proforma-invoices" class="btn-cancel">Batal</a>
        <button type="button" @click="triggerConfirm()" class="btn-save">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                </path>
            </svg>
            SIMPAN DATA
        </button>


    </div>
</div>
