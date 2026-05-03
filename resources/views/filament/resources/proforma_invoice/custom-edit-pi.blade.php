<x-filament-panels::page>
    <style>
        .pi-container {
            font-family: 'Inter', system-ui, sans-serif;
            color: #1e293b;
            background-color: #f8fafc;
            padding: 24px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
        }

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
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

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
            padding: 8px 16px;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .due-btn.active {
            background-color: #2563eb;
            border-color: #2563eb;
            color: white;
        }

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
            padding: 14px;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            text-align: left;
        }

        .pi-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .pi-footer {
            margin-top: 30px;
            background-color: #0f172a;
            color: white;
            padding: 32px;
            border-radius: 16px;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 40px;
        }

        .summary-box {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-label {
            color: #94a3b8;
            font-size: 14px;
        }

        .summary-value {
            font-weight: 600;
            font-size: 16px;
        }

        .grand-total {
            font-size: 28px;
            font-weight: 800;
            color: #38bdf8;
        }

        .btn-add {
            background-color: #0026ff;
            color: #ffffff;
            padding: 10px 18px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-save {
            background-color: #10b981;
            color: white;
            padding: 14px 32px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Perbaikan Modal CSS */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        [x-cloak] {
            display: none !important;
        }

        @media print {

            /* Sembunyikan semua tombol, sidebar filament, dan navigasi */
            .fi-sidebar,
            .fi-topbar,
            .fi-btn,
            button,
            .due-btn,
            .btn-add,
            .btn-save,
            a {
                display: none !important;
            }

            /* Hilangkan padding container dan border agar bersih di kertas */
            .pi-container {
                padding: 0 !important;
                border: none !important;
                background-color: white !important;
            }

            .pi-card,
            .pi-table-wrapper {
                box-shadow: none !important;
                border: 1px solid #eee !important;
            }

            /* Pastikan warna tetap muncul saat diprint (opsional) */
            .pi-footer {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: #0f172a !important;
                color: white !important;
            }
        }
    </style>

    <script>
        let recordId = @js($record->id);

        window.openPrint = function(passedId) {
            const idToPrint = passedId || recordId;

            if (!idToPrint) {
                alert('Data belum tersimpan atau ID tidak ditemukan.');
                return;
            }

            // --- CARA PALING AMPUH AMBIL DATA DARI LIVEWIRE/FILAMENT ---
            let docNumber = idToPrint;
            try {
                // Ambil elemen yang terhubung dengan Livewire
                const wireElement = document.querySelector('[wire\\:id]');
                if (wireElement && window.Livewire) {
                    // Ambil komponen Livewire-nya
                    const component = window.Livewire.find(wireElement.getAttribute('wire:id'));
                    // Ambil property 'data.number' (biasanya Filament menyimpan di properti data)
                    const stateNumber = component.get('data.number');

                    if (stateNumber) {
                        docNumber = stateNumber;
                    }
                }
            } catch (e) {
                console.log('Gagal ambil via Livewire, mencoba cara alternatif...');
                // Alternatif: Ambil langsung dari x-data jika cara di atas gagal
                const alpineData = document.querySelector('[x-data]')?._x_dataStack?.[0];
                if (alpineData?.data?.number) {
                    docNumber = alpineData.data.number;
                }
            }
            // ---------------------------------------------------------

            const frame = document.getElementById('print-frame');
            const url = `/proforma-invoices/${recordId}/print`;

            // Bersihkan nomor untuk jadi nama file
            const cleanNumber = docNumber.toString().replace(/[\/\\?%*:|"<>]/g, '-');
            document.title = `Sales Order - ${cleanNumber}`;

            console.log('Nama File Set:', document.title);
            frame.src = url;

            frame.onload = function() {
                setTimeout(() => {
                    try {
                        frame.contentWindow.focus();
                        frame.contentWindow.print();
                    } catch (e) {
                        console.error('Gagal print:', e);
                    }
                }, 2000);
            };
        };
    </script>

    <div x-data="{
        state: @entangle('data'),
        productList: [],
        brandList: [],
        dueOption: 'custom',
        formattedDate: '',
        isLoading: false,
        showApproveModal: false,
        showUpdatePriceModal: false,
    
        async updatePricesToDatabase() {
            this.isLoading = true;
    
            // Mengambil ID dari URL (asumsi format: .../proforma-invoices/15/edit)
            const pathArray = window.location.pathname.split('/');
            const recordId = pathArray[pathArray.length - 2];
    
            try {
                const response = await fetch('/api/update-product-prices', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        record_id: recordId, // ID PI (15) dikirim ke sini
                        items: this.state.items
                    })
                });
    
                const result = await response.json();
                if (result.success) {
                    alert(result.message);
                    this.showUpdatePriceModal = false;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isLoading = false;
            }
        },
    
        isApproved() {
            return true;
        },
    
        async init() {
            if (!this.state.id && @js($this->record->id)) {
                this.state.id = @js($this->record->id);
            }
    
            if (this.state.ppn_percent === undefined || this.state.ppn_percent === null) {
                this.state.ppn_percent = 11;
            }
    
            const formatDate = (dateStr) => {
                if (!dateStr) return '';
                return dateStr.includes('T') ? dateStr.split('T')[0] : dateStr;
            };
    
            this.state.date = formatDate(this.state.date);
            this.state.delivery_deadline = formatDate(this.state.delivery_deadline);
            this.state.due_date = formatDate(this.state.due_date);
    
            if (!this.state.items) this.state.items = [];
    
            if (this.state.customer_induk_id) {
                await Promise.all([this.loadBrands(), this.loadProducts()]);
            }
    
            this.calculateAll();
    
            this.$watch('state.date', async (val) => {
                if (val && !this.state.number) {
                    this.state.number = await this.$wire.generatePINumber(val);
                }
                if (this.dueOption !== 'custom') this.calculateDue(this.dueOption);
            });
        },
    
        async confirmApprove() {
            this.showApproveModal = false;
            this.isLoading = true;
    
            const token = document.querySelector('meta[name=csrf-token]')?.content ||
                document.querySelector('input[name=_token]')?.value;
    
            try {
                const response = await fetch(`/api/proforma-invoices/${this.state.id}/approve`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });
    
                const result = await response.json();
                if (result.success) {
                    window.location.href = '/erp/proforma-invoices';
                } else {
                    alert('Gagal: ' + result.message);
                }
            } catch (e) {
                alert('Terjadi kesalahan sistem.');
            } finally {
                this.isLoading = false;
            }
        },
    
        async loadProducts() {
            try {
                const res = await fetch('/api/customer-products/' + this.state.customer_induk_id);
                this.productList = await res.json();
            } catch (e) { console.error('Error load products:', e); }
        },
    
        async loadBrands() {
            try {
                const res = await fetch('/api/customer-brands/' + this.state.customer_induk_id);
                this.brandList = await res.json();
            } catch (e) { console.error('Error load brands:', e); }
        },
    
        calculateDue(days) {
            this.dueOption = days;
            if (days === 'custom') return;
            let baseStr = this.state.date || new Date().toISOString().split('T')[0];
            let result = new Date(baseStr);
            result.setDate(result.getDate() + parseInt(days));
            this.state.due_date = result.toISOString().split('T')[0];
            const d = result.getDate().toString().padStart(2, '0');
            const m = (result.getMonth() + 1).toString().padStart(2, '0');
            this.formattedDate = `${d}/${m}/${result.getFullYear()}`;
        },
    
        updateProduct(index) {
            let item = this.state.items[index];
            const prod = this.productList.find(p => p.id == item.product_id);
            if (prod) {
                item.unit_price = prod.price || 0;
                item.hpp_price = prod.hpp || 0; // Set HPP otomatis saat produk dipilih
            }
            this.calculateAll();
        },
    
        calculateAll() {
            let subtotalItems = 0;
            let totalHpp = 0; // Variabel baru untuk hitung total modal
    
            this.state.items.forEach(item => {
                item.subtotal = (item.qty || 0) * (item.unit_price || 0);
                subtotalItems += item.subtotal;
    
                // Hitung akumulasi HPP (Modal x Qty)
                totalHpp += (item.qty || 0) * (item.hpp_price || 0);
            });
    
            this.state.subtotal = subtotalItems;
            this.state.total_hpp = totalHpp; // Simpan ke state agar bisa ditampilkan
    
            const percent = parseFloat(this.state.ppn_percent || 0);
            this.state.ppn_amount = (subtotalItems * percent) / 100;
            this.state.total_amount = this.state.subtotal + this.state.ppn_amount;
    
            // Hitung Estimasi Profit (Total Jual Tanpa Pajak - Total Modal)
            this.state.estimated_profit = this.state.subtotal - totalHpp;
        },
    
        addItem() {
            this.state.items.push({
                product_id: '',
                qty: 1,
                unit_price: 0,
                hpp_price: 0, // Inisialisasi HPP
                subtotal: 0
            });
        },
    
        removeItem(index) {
            this.state.items.splice(index, 1);
            this.calculateAll();
        },
    
        formatIDR(val) {
            return new Intl.NumberFormat('id-ID').format(val || 0);
        },
    
        async saveProformaInvoice() {
            if (!this.state.customer_brand_id) return alert('Silahkan pilih Cabang/Brand!');
            if (this.state.items.length === 0) return alert('Daftar barang tidak boleh kosong!');
    
            this.isLoading = true;
            const token = document.querySelector('meta[name=csrf-token]')?.content ||
                document.querySelector('input[name=_token]')?.value;
    
            try {
                const response = await fetch(`/api/proforma-invoices/${this.state.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(this.state)
                });
    
                const result = await response.json();
                if (result.success) {
                    window.location.href = `/erp/proforma-invoices/${this.state.id}/review`;
                } else {
                    alert('Gagal: ' + result.message);
                }
            } catch (e) {
                alert('Terjadi kesalahan sistem.');
            } finally {
                this.isLoading = false;
            }
        },
    
    
        formatIDR(val) {
            return new Intl.NumberFormat('id-ID').format(val || 0);
        },
    
        calculateMargin(item) {
            if (!item.unit_price || item.unit_price == 0) return 0;
    
            let margin = ((item.unit_price - (item.hpp_price || 0)) / item.unit_price) * 100;
            return margin.toFixed(1);
        },
    }" class="pi-container">

        <div class="pi-card">
            <div class="pi-header-grid">
                <div class="form-group">
                    <label>Nomor PI</label>
                    <input type="text" x-model="state.number" readonly class="form-input"
                        style="background: #f1f5f9; font-weight: bold;">
                </div>
                <div class="form-group">
                    <label>PO Customer</label>
                    <input :disabled="isApproved()" type="text" x-model="state.po_number" class="form-input"
                        placeholder="Contoh: PO/2024/001">
                </div>
                <div class="form-group">
                    <label>Customer Induk</label>
                    <select :disabled="isApproved()" x-model="state.customer_induk_id"
                        @change="loadBrands(); loadProducts();" class="form-input">
                        <option value="">Pilih Customer</option>
                        @foreach (\App\Models\CustomerInduk::all() as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Brand / Cabang <span style="color:red">*</span></label>
                    <select :disabled="isApproved()" x-model.number="state.customer_brand_id" class="form-input">
                        <option :disabled="isApproved()" value="">Pilih Cabang</option>
                        <template x-for="brand in brandList" :key="brand.id">
                            <option :disabled="isApproved()" :value="brand.id" x-text="brand.name"
                                :selected="brand.id == state.customer_brand_id"></option>
                        </template>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal SO</label>
                    <input :disabled="isApproved()" type="date" x-model="state.date" class="form-input">
                </div>
                <div class="form-group">
                    <label>Deadline Pengiriman</label>
                    <input :disabled="isApproved()" type="date" x-model="state.delivery_deadline" class="form-input">
                </div>

                <div class="due-section">
                    <label>Termin Pembayaran</label>
                    <div class="due-options">
                        <template x-for="d in [7, 14, 21, 30]">
                            <button :disabled="isApproved()" type="button" @click="calculateDue(d)"
                                :class="dueOption == d ? 'due-btn active' : 'due-btn'" x-text="d + ' Hari'"></button>
                        </template>
                        <button :disabled="isApproved()" type="button" @click="dueOption = 'custom'"
                            :class="dueOption == 'custom' ? 'due-btn active' : 'due-btn'">Custom</button>
                    </div>
                    <div style="margin-top: 10px;">
                        <input :disabled="isApproved()" x-show="dueOption === 'custom'" type="date"
                            x-model="state.due_date" class="form-input">
                        <div x-show="dueOption !== 'custom'" class="form-input" style="background: #f1f5f9;">
                            <span x-text="formattedDate || '-'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h3 style="font-weight: 700; font-size: 16px;">Item Produk</h3>
            <button x-show="!isApproved()" type="button" class="btn-add" @click="addItem()">
                + Tambah Produk
            </button>
        </div>

        <div class="pi-table-wrapper">
            <table class="pi-table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th style="width: 80px;">Unit</th>
                        <th style="width: 100px;">Qty</th>
                        <th style="width: 180px;">Harga Jual Customer</th>
                        <th style="width: 180px;">Harga HPP</th>
                        <th style="width: 180px;">Margin Keungtungan(%)</th>
                        <th style="width: 180px;">Subtotal</th>
                        <th style="width: 50px;" x-show="!isApproved()"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in state.items" :key="index">
                        <tr>
                            <td>
                                <select :disabled="isApproved()" x-model.number="item.product_id"
                                    @change="updateProduct(index)" class="form-input">
                                    <option value="">Pilih Produk</option>
                                    <template x-for="p in productList" :key="p.id">
                                        <option :value="p.id" x-text="p.name"
                                            :selected="p.id == item.product_id"></option>
                                    </template>
                                </select>
                            </td>
                            <td><span style="font-size: 13px; color: #64748b;"
                                    x-text="productList.find(p => p.id == item.product_id)?.unit || '-'"></span></td>
                            <td><input :disabled="isApproved()" type="number" x-model.number="item.qty"
                                    @input="calculateAll()" class="form-input"></td>
                            <td><input :disabled="isApproved()" type="number" x-model.number="item.unit_price"
                                    @input="calculateAll()" class="form-input"></td>
                            <td>
                                <input :disabled="isApproved()" type="number" x-model.number="item.hpp_price"
                                    @input="calculateAll()" class="form-input"
                                    style="background: #f8fafc; color: #64748b;">
                            </td>
                            <td style="font-weight: 700; color: #38bdf8;">
                                <span x-text="calculateMargin(item) + '%'"></span>
                            </td>
                            <td style="font-weight: 700;">Rp <span x-text="formatIDR(item.subtotal)"></span></td>
                            <td x-show="!isApproved()">
                                <button x-show="!isApproved()" type="button" @click="removeItem(index)"
                                    style="color:#ef4444; background:none; border:none; cursor:pointer;">
                                    🗑️
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="pi-footer">
            <div>
                <label
                    style="color: #94a3b8; font-size: 12px; font-weight: 700; margin-bottom: 8px; display: block;">CATATAN
                    INTERNAL</label>
                <textarea :disabled="isApproved()" x-model="state.notes" class="form-input"
                    style="background: #1e293b; color: white; border-color: #334155;" rows="3"
                    placeholder="Tambahkan catatan..."></textarea>

                <div
                    style="margin-top: 20px; display: flex; align-items: center; gap: 15px; background: #1e293b; padding: 15px; border-radius: 10px; border: 1px solid #334155;">
                    <div>
                        <label
                            style="display: block; font-size: 11px; color: #94a3b8; margin-bottom: 5px; font-weight: bold;">SET
                            PPN (%)</label>
                        <input :disabled="isApproved()" type="number" x-model.number="state.ppn_percent"
                            @input="calculateAll()"
                            style="width: 80px; background: #0f172a; border: 1px solid #38bdf8; color: #38bdf8; padding: 8px; border-radius: 6px; font-weight: bold; text-align: center;">
                    </div>
                    <div style="color: #94a3b8; font-size: 13px;">
                        Jika tidak ada pajak, isi dengan angka <span style="color: white; font-weight: bold;">0</span>.
                    </div>
                </div>
            </div>

            <div class="summary-box">
                <div class="summary-row">
                    <span class="summary-label">Subtotal (DPP)</span>
                    <span class="summary-value">Rp <span x-text="formatIDR(state.subtotal)"></span></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">PPN (<span x-text="state.ppn_percent || 0"></span>%)</span>
                    <span class="summary-value" style="color: #38bdf8">
                        Rp <span x-text="formatIDR(state.ppn_amount)"></span>
                    </span>
                </div>
                <div style="border-top: 1px solid #334155; margin: 8px 0; padding-top: 8px;" class="summary-row">
                    <span class="summary-label" style="color: white; font-weight: 700;">TOTAL AKHIR</span>
                    <span class="grand-total">Rp <span x-text="formatIDR(state.total_amount)"></span></span>
                </div>
            </div>
        </div>



        <div style="margin-top: 24px; display: flex; justify-content: end; align-items: center;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <span x-show="isLoading" style="font-size: 13px; color: #64748b;">Memproses...</span>
                <a href="/erp/proforma-invoices" class="due-btn"
                    style="text-decoration: none; padding: 12px 24px; background: rgb(222, 0, 0); color: white">Kembali</a>


                <button type="button" @click="openPrint(state.number)"
                    style="background-color: #6366f1; color: white; padding: 14px 32px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <span>🖨️</span> PRINT PDF
                </button>

                <button type="button" @click="showUpdatePriceModal = true"
                    style="background-color: #f59e0b; color: white; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none;">
                    💰 Update Harga Cepat
                </button>


                <div class="flex gap-2">
                    <template x-if="state.status === 'Created'">
                        <button type="button" @click="saveProformaInvoice()" class="btn-revision"
                            :disabled="isLoading">
                            <span x-text="isLoading ? 'MENGIRIM REVISI...' : '📤 AJUKAN REVISI BARU'"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <div x-show="showApproveModal" class="modal-overlay" x-cloak
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div @click.away="showApproveModal = false" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                style="background: white; width: 100%; max-width: 450px; border-radius: 20px; padding: 32px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); position: relative;">

                <div
                    style="width: 60px; height: 60px; background: #eff6ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                    <span style="font-size: 30px;">📄</span>
                </div>

                <h3 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Konfirmasi Approval
                </h3>
                <p style="font-size: 15px; color: #64748b; line-height: 1.6; margin-bottom: 24px;">
                    Apakah Anda yakin ingin menyetujui Proforma Invoice <span style="font-weight: 700; color: #0f172a;"
                        x-text="state.number"></span>? Tindakan ini tidak dapat dibatalkan.
                </p>

                <div style="display: flex; gap: 12px;">
                    <button type="button" @click="showApproveModal = false"
                        style="flex: 1; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; color: #475569; font-weight: 600; cursor: pointer;">
                        Batal
                    </button>
                    <button type="button" @click="confirmApprove()"
                        style="flex: 1; padding: 12px; border-radius: 12px; border: none; background: #3b82f6; color: white; font-weight: 700; cursor: pointer;">
                        Ya, Approve
                    </button>
                </div>

                <button @click="showApproveModal = false"
                    style="position: absolute; top: 20px; right: 20px; background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 20px;">
                    &times;
                </button>
            </div>
        </div>

        <div x-show="showUpdatePriceModal" class="modal-overlay" x-cloak
            style="display: flex; align-items: center; justify-content: center;">
            <div @click.away="showUpdatePriceModal = false"
                style="background: white; width: 95%; max-width: 1000px; border-radius: 16px; padding: 28px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); max-height: 85vh; display: flex; flex-direction: column;">

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin: 0;">Update Harga Jual Barang
                    </h3>
                    <button @click="showUpdatePriceModal = false"
                        style="background: none; border: none; cursor: pointer; color: #64748b; font-size: 24px;">&times;</button>
                </div>

                <div style="flex-grow: 1; overflow-y: auto; margin: 0 -28px; padding: 0 28px;">
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                        <thead>
                            <tr style="background: #1e40af; color: white;">
                                <th
                                    style="padding: 14px 16px; text-align: left; border-radius: 8px 0 0 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Produk</th>
                                <th
                                    style="padding: 14px 16px; text-align: right; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; width: 220px;">
                                    Harga Jual Baru</th>
                                <th
                                    style="padding: 14px 16px; text-align: right; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; width: 200px;">
                                    Harga HPP Baru</th>
                                <th
                                    style="padding: 14px 16px; text-align: center; border-radius: 0 8px 8px 0; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; width: 100px;">
                                    Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in state.items" :key="index">
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 20px 16px; vertical-align: middle;">
                                        <div style="font-weight: 700; color: #334155; font-size: 15px;"
                                            x-text="productList.find(p => p.id == item.product_id)?.name || 'Produk tidak dikenal'">
                                        </div>
                                        <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Harga Saat Ini:
                                            <span style="font-weight: 600;"
                                                x-text="'Rp ' + formatIDR(item.unit_price)"></span>
                                        </div>
                                    </td>
                                    <td style="padding: 16px; text-align: right;">
                                        <div style="position: relative; display: inline-block; width: 100%;">
                                            <span
                                                style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-weight: bold; color: #94a3b8;">Rp</span>
                                            <input type="number" x-model.number="item.unit_price"
                                                @input="calculateAll()"
                                                style="width: 100%; padding: 10px 12px 10px 40px; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 600; text-align: right; font-size: 14px;">
                                        </div>
                                    </td>
                                    <td style="padding: 16px; text-align: right;">
                                        <div style="position: relative; display: inline-block; width: 100%;">
                                            <span
                                                style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-weight: bold; color: #94a3b8;">Rp</span>
                                            <input type="number" x-model.number="item.hpp_price"
                                                @input="calculateAll()"
                                                style="width: 100%; padding: 10px 12px 10px 40px; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 600; text-align: right; font-size: 14px;">
                                        </div>
                                    </td>
                                    <td style="padding: 16px; text-align: center;">
                                        <span
                                            :style="calculateMargin(item) < 10 ? 'color: #ef4444; background: #fee2e2' :
                                                'color: #10b981; background: #dcfce7'"
                                            style="font-weight: 800; padding: 6px 10px; border-radius: 6px; font-size: 13px;"
                                            x-text="calculateMargin(item) + '%'">
                                        </span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div
                    style="margin-top: 32px; display: flex; justify-content: flex-end; gap: 12px; border-top: 2px solid #f1f5f9; padding-top: 24px;">
                    <button type="button" @click="showUpdatePriceModal = false"
                        style="padding: 12px 28px; border-radius: 12px; border: 1px solid #cbd5e1; background: white; color: #64748b; font-weight: 700; cursor: pointer; transition: all 0.2s;">
                        Batal
                    </button>

                    <button type="button" @click="updatePricesToDatabase()" :disabled="isLoading"
                        style="padding: 12px 32px; border-radius: 12px; border: none; background: #10b981; color: white; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: opacity 0.2s;">
                        <template x-if="!isLoading">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span>💾</span>
                                <span>Simpan ke Database</span>
                            </div>
                        </template>
                        <template x-if="isLoading">
                            <span>⏳ Memproses...</span>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!-- Iframe untuk render halaman print tanpa tab baru -->
    <iframe id="print-frame" style="width:0;height:0;border:0;position:absolute;left:-9999px;"
        aria-hidden="true"></iframe>

</x-filament-panels::page>
