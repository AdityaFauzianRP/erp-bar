<x-filament-panels::page>
    <style>
        .po-wrapper {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            padding-bottom: 50px;
        }

        .po-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 1024px) {
            .po-grid {
                grid-template-columns: 2fr 1fr;
                gap: 30px;
            }
        }

        .po-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .po-input {
            width: 100%;
            background: #f8fafc;
            border: 2px solid #f1f5f9;
            border-radius: 12px;
            padding: 12px;
            font-size: 0.95rem;
            transition: 0.2s;
        }

        .po-input:focus {
            border-color: #3b82f6;
            background: #fff;
            outline: none;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        @media (max-width: 767px) {
            .po-table thead {
                display: none;
            }

            .po-table tr {
                display: block;
                background: #f8fafc;
                margin-bottom: 15px;
                border-radius: 12px;
                padding: 15px;
                border: 1px solid #e2e8f0;
            }

            .po-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                width: 100%;
                border: none;
            }

            .mobile-label {
                display: block;
                font-size: 0.7rem;
                font-weight: 800;
                color: #64748b;
                text-transform: uppercase;
            }
        }

        @media (min-width: 768px) {
            .po-table {
                width: 100%;
                border-collapse: collapse;
            }

            .po-table th {
                text-align: left;
                padding: 12px;
                border-bottom: 2px solid #f1f5f9;
                color: #64748b;
                font-size: 0.8rem;
            }

            .po-table td {
                padding: 15px 10px;
                vertical-align: middle;
                border-bottom: 1px solid #f1f5f9;
            }

            .mobile-label {
                display: none;
            }
        }

        .input-price {
            height: 45px;
            text-align: right;
            font-weight: 700;
        }

        .input-qty {
            height: 45px;
            font-weight: 800;
            text-align: center;
            color: #3b82f6;
        }

        .summary-box {
            background: #0f172a;
            color: white;
            border-radius: 16px;
            padding: 20px;
        }

        .grand-total-amount {
            font-size: 2.2rem;
            font-weight: 900;
            color: #fff;
        }

        .btn-main {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-weight: 800;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-save {
            background: #3b82f6;
            color: white;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js" defer></script>

    <div class="po-wrapper" x-data="{
        items: @entangle('items'),
        tax_rate: @entangle('tax_rate'),
    
        get subtotal() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.price || 0) * parseFloat(item.qty || 0)), 0);
        },
        get tax_amount() {
            return this.subtotal * (parseFloat(this.tax_rate || 0) / 100);
        },
        get grand_total() {
            return this.subtotal + this.tax_amount;
        },
        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(number);
        },
        updatePrice(index, val) {
            let clean = val.replace(/[^0-9,]/g, '').replace(',', '.');
            this.items[index].price = parseFloat(clean) || 0;
        }
    }">

        <div class="po-grid">
            {{-- KOLOM KIRI --}}
            <div class="po-main-content">
                <div class="po-card" style="margin-bottom: 20px;">
                    <h3 style="font-weight: 800; margin-bottom: 20px;">📝 Transaksi Baru</h3>
                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; color: #64748b;">SUPPLIER</label>
                            <select wire:model.live="supplier_id" class="po-input">
                                <option value="">Pilih Supplier...</option>
                                @foreach (\App\Models\Supplier::all() as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; color: #64748b;">TANGGAL
                                PEMBELIAN</label>
                            <input type="date" wire:model.live="created_at" class="po-input">
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; color: #64748b;">TANGGAL
                                PENERIMAAN</label>
                            <input type="date" wire:model.live="due_date" class="po-input">
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; color: #64748b;">PPN (%)</label>
                            <input type="number" x-model="tax_rate" class="po-input" onfocus="this.select()">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 700; color: #64748b;">CATATAN / NOTES</label>
                        <textarea wire:model.defer="notes" class="po-input" rows="2" placeholder="Tambahkan keterangan..."
                            style="resize: none;"></textarea>
                    </div>
                </div>

                <div class="po-card">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="font-weight: 800; margin-top:0;">🛒 Item Barang</h3>
                        <div x-data="{}"
                            x-on:keydown.window.ctrl.b.prevent="$dispatch('open-modal', { id: 'search-modal' })">
                            <button class="po-input"
                                style="width:auto; background:#eff6ff; color:#3b82f6; border-style:dashed; font-weight:700;"
                                x-on:click="$dispatch('open-modal', { id: 'search-modal' })">
                                Tambah Produk | CTRL + B
                            </button>
                        </div>
                    </div>

                    <table class="po-table">
                        <thead>
                            <tr>
                                <th>PRODUK</th>
                                <th style="text-align: right;">HARGA SATUAN</th>
                                <th style="text-align: center;">QTY</th>
                                <th style="text-align: right;">SUBTOTAL</th>
                                <th style="text-align: center;">SATUAN</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="item.product_id">
                                <tr>
                                    <td>
                                        <div style="font-weight: 800;" x-text="item.name"></div>
                                        <div style="font-size: 0.7rem; color: #94a3b8; font-family: monospace;"
                                            x-text="item.code"></div>
                                    </td>
                                    <td>
                                        <div style="position: relative; display: flex; align-items: center;">
                                            <span
                                                style="position: absolute; left: 10px; font-weight: bold; color: #64748b;">Rp</span>
                                            <input type="number" step="any" x-model="item.price"
                                                class="po-input input-price" style="padding-left: 35px;"
                                                onfocus="this.select()">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" x-model="item.qty"
                                            class="po-input input-qty" onfocus="this.select()">
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="font-weight: 800; font-size: 1.1rem;"
                                            x-text="'Rp' + formatRupiah(item.price * item.qty)">
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 6px; font-size: 0.85rem; font-weight: 700;"
                                            x-text="item.unit || '-'">
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <button @click="items.splice(index, 1)" style="color:#f87171;">
                                            <svg width="20" height="20" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path
                                                    d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- KOLOM KANAN (SUMMARY) --}}
            <div class="po-sidebar">
                <div class="po-card" style="position: sticky; top: 20px;">
                    <div class="summary-box">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; opacity: 0.8;">
                            <span>Subtotal</span>
                            <span style="font-weight: 700;" x-text="'Rp' + formatRupiah(subtotal)"></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; opacity: 0.7;">
                            <span x-text="'PPN (' + tax_rate + '%)'"></span>
                            <span x-text="'Rp' + formatRupiah(tax_amount)"></span>
                        </div>
                        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                            <div style="color: #3b82f6; font-size: 0.75rem; font-weight: 800;">GRAND TOTAL</div>
                            <div class="grand-total-amount" x-text="'Rp' + formatRupiah(grand_total)"></div>
                        </div>
                    </div>

                    <button class="btn-main" style="background: #3b82f6; color: white; margin-top: 20px;"
                        wire:click="save">
                        SIMPAN TRANSAKSI
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CARI BARANG (Logika DB tetap di Blade Directive karena butuh data model) --}}
    <x-filament::modal id="search-modal" width="4xl" :display-confirm-action="false">
        <x-slot name="heading">Katalog Produk</x-slot>
        <div style="padding: 10px 0;">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="po-input"
                style="margin-bottom: 20px; border: 2px solid #3b82f6;">
            <div style="max-height: 400px; overflow-y: auto;">
                <table class="po-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;"></th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $searchProducts = \App\Models\Product::query()
                                ->whereHas('suppliers', fn($q) => $q->where('supplier_id', $this->supplier_id))
                                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                                ->with(['unit'])
                                ->get();
                        @endphp
                        @foreach ($searchProducts as $p)
                            <tr style="cursor: pointer;"
                                onclick="document.getElementById('check-{{ $p->id }}').click()">
                                <td>
                                    <input type="checkbox" id="check-{{ $p->id }}"
                                        wire:model="selectedProductIds" value="{{ $p->id }}"
                                        onclick="event.stopPropagation()">
                                </td>
                                <td>{{ $p->code }}</td>
                                <td style="font-weight: 700;">{{ $p->name }}</td>
                                <td>{{ $p->unit->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 25px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 700; color: #64748b;">{{ count($selectedProductIds) }} terpilih</span>
                <button wire:click="addSelectedProducts" class="btn-main"
                    style="width: auto; padding: 12px 30px; background: #3b82f6; color: white;">
                    Tambahkan Ke List
                </button>
            </div>
        </div>
    </x-filament::modal>

    <script>
        window.addEventListener('keydown', e => {
            if (e.key === 'F2') $dispatch('open-modal', {
                id: 'search-modal'
            });
        });
    </script>
</x-filament-panels::page>
