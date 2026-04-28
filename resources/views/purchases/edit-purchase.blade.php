<x-filament-panels::page>
    @php
        $purchaseRecord = $this->getRecord();
        $isEditable = in_array($purchaseRecord->status, ['Menunggu Approval', 'Ordered']);
    @endphp

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
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        @media (min-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr 1fr 1fr;
            }
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
            outline: none;
            background: #fff;
        }

        .po-table {
            width: 100%;
            border-collapse: collapse;
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
            color: #ffffff;
            line-height: 1;
        }

        .btn-main {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-weight: 800;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.2s;
            text-align: center;
        }

        .btn-main:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>

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
            return new Intl.NumberFormat('id-ID').format(number);
        }
    }">
        <div class="po-grid">
            {{-- Bagian Kiri: Form & Tabel --}}
            <div class="po-main-content">
                <div class="po-card">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="font-weight: 800; font-size: 1.25rem;">📝 Edit Purchase
                            #{{ $purchaseRecord->purchase_number }}</h2>
                        <span
                            style="padding: 5px 12px; background: #eff6ff; color: #3b82f6; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                            {{ $purchaseRecord->status }}
                        </span>
                    </div>

                    <div class="info-grid">
                        <div>
                            <label
                                style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 5px; display: block;">TANGGAL</label>
                            <input type="date" wire:model.live="created_at" class="po-input"
                                @disabled(!$isEditable)>
                        </div>
                        <div>
                            <label
                                style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 5px; display: block;">SUPPLIER</label>
                            <select wire:model.live="supplier_id" class="po-input" @disabled(!$isEditable)>
                                @foreach (\App\Models\Supplier::all() as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 5px; display: block;">PPN
                                (%)</label>
                            <input type="number" x-model="tax_rate" class="po-input" onfocus="this.select()"
                                @disabled(!$isEditable)>
                        </div>
                    </div>
                </div>

                <div class="po-card">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="font-weight: 800; margin:0;">🛒 Daftar Barang</h3>
                        @if ($isEditable)
                            <button class="po-input"
                                style="width:auto; background:#eff6ff; color:#3b82f6; border-style:dashed; cursor: pointer;"
                                x-on:click="$dispatch('open-modal', { id: 'search-modal' })">
                                + Cari Produk (F2)
                            </button>
                        @endif
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="po-table">
                            <thead>
                                <tr
                                    style="text-align: left; border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 0.8rem;">
                                    <th style="padding: 10px;">PRODUK</th>
                                    <th style="text-align: right; padding: 10px;">HARGA SATUAN</th>
                                    <th style="text-align: center; padding: 10px; width: 100px;">QTY</th>
                                    <th style="text-align: right; padding: 10px;">SUBTOTAL</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr style="border-bottom: 1px solid #f8fafc;">
                                        <td style="padding: 12px 10px;">
                                            <div style="font-weight: 700;" x-text="item.name"></div>
                                            <div style="font-size: 0.7rem; color: #94a3b8;"
                                                x-text="item.code + ' | ' + item.unit"></div>
                                        </td>
                                        <td>
                                            <div style="position: relative; display: flex; align-items: center;">
                                                <span
                                                    style="position: absolute; left: 10px; font-weight: bold; color: #64748b;">Rp</span>
                                                <input type="number" x-model="item.price" class="po-input input-price"
                                                    style="padding-left: 35px;" onfocus="this.select()"
                                                    @disabled(!$isEditable)>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" x-model="item.qty" class="po-input input-qty"
                                                onfocus="this.select()" @disabled(!$isEditable)>
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="font-weight: 800; color: #0f172a; font-size: 1.1rem;">
                                                Rp<span x-text="formatRupiah(item.price * item.qty)"></span>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            @if ($isEditable)
                                                <button type="button" @click="items.splice(index, 1)"
                                                    style="color:#f87171;">
                                                    <x-heroicon-m-trash class="w-5 h-5" />
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Summary --}}
            <div class="po-sidebar">
                <div class="po-card" style="position: sticky; top: 20px;">
                    <div class="summary-box">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; opacity: 0.8;">
                            <span>Subtotal</span>
                            <span>Rp<span x-text="formatRupiah(subtotal)"></span></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; opacity: 0.8;">
                            <span>PPN (<span x-text="tax_rate"></span>%)</span>
                            <span>Rp<span x-text="formatRupiah(tax_amount)"></span></span>
                        </div>
                        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                            <div style="color: #3b82f6; font-size: 0.7rem; font-weight: 800;">GRAND TOTAL</div>
                            <div class="grand-total-amount">Rp<span x-text="formatRupiah(grand_total)"></span></div>
                        </div>
                    </div>

                    @if ($isEditable)
                        <button class="btn-main" style="background: #3b82f6; color: white;" wire:click="save">
                            SIMPAN PERUBAHAN
                        </button>

                        @can('ApprovePurchase')
                            <button class="btn-main" style="background: #10b981; color: white;"
                                wire:click="openApproveModal">
                                APPROVE PO
                            </button>
                        @endcan
                    @endif

                    <button class="btn-main" style="background: #f1f5f9; color: #475569;" wire:click="printPO">
                        CETAK PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL SEARCH --}}
    <x-filament::modal id="search-modal" width="4xl" :display-confirm-action="false">
        <x-slot name="heading">Cari Produk dari Supplier Terpilih</x-slot>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama produk..."
            class="po-input" style="margin-bottom: 15px; border-color: #3b82f6;">

        <div style="max-height: 300px; overflow-y: auto;">
            <table class="po-table">
                @php
                    $searchProducts = \App\Models\Product::query()
                        ->whereHas('suppliers', fn($q) => $q->where('supplier_id', $this->supplier_id))
                        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                        ->get();
                @endphp
                @foreach ($searchProducts as $p)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px;">
                            <input type="checkbox" wire:model="selectedProductIds" value="{{ $p->id }}">
                        </td>
                        <td style="font-weight: 700;">{{ $p->name }}</td>
                        <td style="color: #64748b;">{{ $p->code }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
        <x-filament::button wire:click="addSelectedProducts" class="mt-4 w-full">Tambahkan Produk
            Terpilih</x-filament::button>
    </x-filament::modal>

    {{-- MODAL CONFIRM APPROVE --}}
    <x-filament::modal id="confirm-approve-modal" width="sm">
        <x-slot name="heading">Konfirmasi</x-slot>
        <p class="text-center text-gray-500">Apakah Anda yakin ingin menyetujui PO ini? Status akan berubah menjadi
            <strong>Ordered</strong>.
        </p>
        <div class="flex justify-center gap-3 mt-5">
            <x-filament::button color="gray" x-on:click="close">Batal</x-filament::button>
            <x-filament::button color="success" wire:click="approve">Ya, Approve</x-filament::button>
        </div>
    </x-filament::modal>

    <iframe id="print-frame" style="display:none;"></iframe>
    <script>
        window.addEventListener('trigger-print', e => {
            const frame = document.getElementById('print-frame');
            frame.src = e.detail.url;
            frame.onload = () => {
                frame.contentWindow.print();
            };
        });
        window.addEventListener('keydown', e => {
            if (e.key === 'F2') $dispatch('open-modal', {
                id: 'search-modal'
            });
        });
    </script>
</x-filament-panels::page>
