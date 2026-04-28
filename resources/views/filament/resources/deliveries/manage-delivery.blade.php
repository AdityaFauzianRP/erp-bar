<x-filament-panels::page>
    {{-- 1. KULIT (Modern Full Width CSS) --}}
    <style>
        /* Memaksa Filament Page menjadi Full Width */
        .fi-main-ctn {
            max-width: none !important;
        }

        .fi-page-content {
            max-width: none !important;
        }

        :root {
            --primary: #4f46e5;
            --bg-body: #f1f5f9;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .delivery-wrapper {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 100%;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-header h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .grid-master {
            display: grid;
            grid-template-columns: 1fr 1fr;
            /* Membagi Info Utama & Logistik berdampingan */
            gap: 1.5rem;
        }

        .card-modern {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            border: 1px solid #e2e8f0;
        }

        .full-width-card {
            grid-column: span 2;
        }

        /* Styling Input */
        .input-group {
            margin-bottom: 1.25rem;
        }

        .input-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .input-field {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.2s;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        /* Table Design */
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .modern-table th {
            background: #f8fafc;
            padding: 1rem;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
        }

        .modern-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .selection-full {
            height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 3px dashed #cbd5e1;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <div x-data="deliveryApp()" class="delivery-wrapper" x-cloak>

        {{-- SECTION 1: PILIH PI (Hanya muncul jika belum pilih) --}}
        <template x-if="!isEdit && !piSelected">
            <div class="card-modern" style="min-height: 500px;">
                <div class="section-header">
                    <div style="background: #eef2ff; color: #4f46e5; padding: 0.5rem; border-radius: 10px;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <h2 style="font-size: 1.5rem; font-weight: 800; margin-left: 10px;">Pilih Penjualan untuk
                        Kirim Barang</h2>
                </div>

                {{-- Input Search --}}
                <div style="margin-bottom: 1.5rem; position: relative;">
                    <input type="text" x-model="searchPi" placeholder="Cari Nomor PI atau Nama Customer..."
                        class="input-field" style="padding-left: 2.5rem; height: 50px; font-size: 1rem;">
                    <div style="position: absolute; left: 1rem; top: 15px; color: #94a3b8;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Table Selection --}}
                <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Nomor PI</th>
                                <th>Customer</th>
                                <th>Tgl Keluar</th>
                                <th>Status</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $pis = \App\Models\ProformaInvoice::with(['customerInduk'])
                                    ->whereIn('status', ['Created', 'Shipping', 'Delivery Created'])
                                    // Tambahkan filter untuk pending_changes yang kosong/null
                                    ->where(function ($query) {
                                        $query
                                            ->whereNull('pending_changes')
                                            ->orWhere('pending_changes', '')
                                            ->orWhere('pending_changes', '[]');
                                    })
                                    ->get()
                                    ->map(function ($pi) {
                                        return [
                                            'id' => $pi->id,
                                            'number' => $pi->number,
                                            'customer' => $pi->customerInduk->name ?? 'N/A',
                                            'date' => $pi->date->format('d M Y'),
                                            'status' => $pi->status,
                                        ];
                                    });
                            @endphp

                            <template x-for="pi in filteredPis" :key="pi.id">
                                <tr class="hover-row" style="cursor: pointer;" @click="handlePiSelection(pi.id)">
                                    <td style="font-weight: 700; color: var(--primary);" x-text="pi.number"></td>
                                    <td x-text="pi.customer"></td>
                                    <td x-text="pi.date"></td>
                                    <td>
                                        <span
                                            style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: #f1f5f9; color: #475569;"
                                            x-text="pi.status"></span>
                                    </td>
                                    <td style="text-align: center;">
                                        <button class="fi-btn"
                                            style="background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 6px; font-size: 12px;">
                                            Pilih Penjualan
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- AREA FORM (Muncul setelah PI dipilih atau Mode Edit) --}}
        <div x-show="isEdit || piSelected" class="delivery-wrapper">

            {{-- Action Header --}}
            <div class="card-modern"
                style="padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 800;"
                        x-text="isEdit ? 'Update Pengiriman: ' + formData.no_sj : 'Pembuatan Surat Jalan Baru'"></h2>
                    <p style="font-size: 0.875rem; color: #64748b;" x-text="'Referensi PI: ' + formData.pi_number"></p>
                </div>


                <div style="display: flex; gap: 1rem;">
                    {{-- Tombol dinamis berdasarkan isEdit --}}
                    <button :disabled="formData.status === 'delivered'" @click="saveData()" class="fi-btn"
                        :style="isEdit ?
                            'background: #10b981; color: white; padding: 0.75rem 2rem; border-radius: 8px; font-weight: 600;' :
                            'background: var(--primary); color: white; padding: 0.75rem 2rem; border-radius: 8px; font-weight: 600;'">

                        <span x-show="!loading">
                            <template x-if="!isEdit">
                                <span>Simpan Data Pengiriman</span>
                            </template>
                            <template x-if="isEdit">
                                <span>Simpan Data</span>
                            </template>
                        </span>
                        <span x-show="loading">Memproses...</span>
                    </button>

                    {{-- Ganti bagian tombol cetak dengan ini --}}
                    <button x-show="isEdit" @click="$dispatch('trigger-print', { url: '/delivery/print/' + recordId })"
                        type="button"
                        style="background: #5c5c5c; color: white; padding: 0.75rem 2rem; border-radius: 8px; font-weight: 600; cursor: pointer;">
                        <span>Cetak Surat Jalan</span>
                    </button>

                </div>
            </div>

            <div class="grid-master">
                {{-- SECTION 2: DATA PENGIRIMAN --}}
                <section class="card-modern">
                    <div class="section-header">
                        <svg style="width: 20px; color: var(--primary);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3>Informasi Pengiriman</h3>
                    </div>
                    <div class="input-group">
                        <label>Nomor Surat Jalan</label>
                        <input type="text" x-model="formData.no_sj" class="input-field" readonly
                            style="background: #f8fafc;">
                    </div>
                    {{-- Input status hanya muncul jika isEdit true --}}
                    <div class="input-group" x-show="isEdit">
                        <label>Status Saat Ini</label>
                        <select disabled x-model="formData.status" class="input-field"
                            :style="formData.status === 'delivered' ?
                                'border-color: #10b981; color: #10b981; font-weight: bold;' : ''">
                            <option value="draft">DRAFT (Persiapan)</option>
                            <option value="shipping">Proses Pengiriman</option>
                            <option value="delivered">DELIVERED (Selesai/Diterima)</option>
                        </select>
                    </div>

                    {{-- Info status static jika bukan mode edit (opsional agar user tahu statusnya apa) --}}

                </section>

                {{-- SECTION 3: DATA LOGISTIK (Supir & Kendaraan) --}}
                <section class="card-modern">
                    <div class="section-header">
                        <svg style="width: 20px; color: var(--primary);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            <path
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0">
                            </path>
                        </svg>
                        <h3>Data Logistik</h3>
                    </div>
                    <div class="input-group">
                        <label>Nama Driver / Kurir</label>
                        <input :disabled="formData.status === 'delivered'" type="text" x-model="formData.driver_name"
                            class="input-field" placeholder="Masukkan nama supir...">
                    </div>
                    <div class="input-group">
                        <label>Nomor Plat Kendaraan</label>
                        <input :disabled="formData.status === 'delivered'" type="text"
                            x-model="formData.vehicle_plate" class="input-field" placeholder="Contoh: B 1234 ABC">
                    </div>
                </section>

                <section class="card-modern full-width-card"
                    style="border-left: 6px solid var(--primary); background: #f8fafc;">
                    <div class="section-header">
                        <svg style="width: 20px; color: var(--primary);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <h3>Tujuan & Identitas Pengiriman</h3>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem;">
                        <div>
                            <label
                                style="display: block; font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Customer
                                Induk</label>
                            <div style="font-size: 1rem; font-weight: 800; color: #1e293b; margin-top: 0.25rem;"
                                x-text="formData.customer_name"></div>
                        </div>
                        <div>
                            <label
                                style="display: block; font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Brand
                                / Merk</label>
                            <div style="font-size: 1rem; font-weight: 800; color: #000000; margin-top: 0.25rem;"
                                x-text="formData.brand_name"></div>
                        </div>
                        <div>
                            <label
                                style="display: block; font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Cabang</label>
                            <div style="font-size: 1rem; font-weight: 800; color: #1e293b; margin-top: 0.25rem;"
                                x-text="formData.nama_cabang || '-'"></div>
                        </div>
                        <div>
                            <label
                                style="display: block; font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">No.
                                Penjualan</label>
                            <div style="font-size: 1rem; font-weight: 600; color: #94a3b8; margin-top: 0.25rem;"
                                x-text="'' + formData.pi_number"></div>
                        </div>

                        {{-- this.formData.pi_number --}}

                    </div>
                </section>

                {{-- SECTION 4: DETAIL ITEM (FULL WIDTH) --}}
                <section class="card-modern full-width-card">
                    <div class="section-header">
                        <svg style="width: 20px; color: var(--primary);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3>Daftar Barang dalam Pengiriman</h3>
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Nama Produk</th>
                                    <th style="width: 20%;">Gudang Asal</th> {{-- Kolom Baru --}}
                                    <th style="text-align: center;">Total Order</th>
                                    <th style="text-align: center;">Sisa Kirim</th>
                                    <th style="text-align: center; background: #eef2ff;">Qty Kirim</th>

                                    {{-- Template Status Check --}}
                                    <template x-if="['shipping', 'delivered'].includes(formData.status)">
                                        <th style="text-align: center; background: #f0fdf4;">Diterima</th>
                                    </template>
                                    <template x-if="['shipping', 'delivered'].includes(formData.status)">
                                        <th style="text-align: center; background: #fef2f2;">Rusak</th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in formData.items" :key="index">
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700; font-size: 0.95rem;"
                                                x-text="item.product_name"></div>
                                            <div style="font-size: 10px; color: #94a3b8;">
                                                ID Ref: <span x-text="item.proforma_invoice_item_id"></span>
                                            </div>
                                        </td>

                                        {{-- KOLOM PILIH GUDANG & STOK --}}
                                        <td>
                                            <select :disabled="formData.status === 'delivered'"
                                                x-model="item.warehouse_id" @change="fetchItemStock(item)"
                                                class="input-field"
                                                style="font-size: 0.85rem; padding: 0.5rem; margin-bottom: 4px;">
                                                <option value="">-- Pilih Gudang --</option>
                                                @foreach (\App\Models\Warehouse::all() as $warehouse)
                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            {{-- Badge Stok --}}
                                            <div x-show="!isEdit && item.warehouse_id">
                                                <span style="font-size: 11px; font-weight: 600;"
                                                    :style="item.stock_available < item.qty_shipped ? 'color: #ef4444' :
                                                        'color: #10b981'">
                                                    Stok: <span x-text="item.stock_available || 0"></span>
                                                </span>
                                            </div>
                                        </td>

                                        <td style="text-align: center;" x-text="item.qty_order"></td>
                                        <td style="text-align: center; color: var(--primary); font-weight: 700;"
                                            x-text="item.qty_sisa_kirim"></td>

                                        <td style="text-align: center; background: #f8faff;">
                                            <input :disabled="formData.status === 'delivered'" type="number"
                                                x-model="item.qty_shipped" class="input-field"
                                                style="width: 80px; text-align: center; border-color: var(--primary); font-weight: bold;"
                                                @input="validateQty(item)">
                                        </td>

                                        {{-- Status Delivered --}}
                                        <template x-if="['shipping', 'delivered'].includes(formData.status)">
                                            <td style="text-align: center; background: #fafffb;">
                                                <input :disabled="formData.status === 'delivered'" type="number"
                                                    x-model="item.qty_received_good" class="input-field"
                                                    style="width: 70px; text-align: center;">
                                            </td>
                                        </template>
                                        <template x-if="['shipping', 'delivered'].includes(formData.status)">
                                            <td style="text-align: center; background: #fffafa;">
                                                <input :disabled="formData.status === 'delivered'" type="number"
                                                    x-model="item.qty_wasted" class="input-field"
                                                    style="width: 70px; text-align: center;">
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- SECTION 5: LAINNYA --}}
                <section class="card-modern full-width-card">
                    <div class="section-header">
                        <h3>Catatan Tambahan</h3>
                    </div>
                    <textarea :disabled="formData.status === 'delivered'" class="input-field" rows="3"
                        placeholder="Tambahkan catatan jika ada..." x-model="formData.notes"></textarea>
                </section>
            </div>
        </div>
    </div>

    {{-- 3. SARAF (Logic - Tetap sama namun pastikan variabel sinkron) --}}
    <script>
        let nomorSJ = ""

        const showToast = (message, type = 'danger') => {
            // Hapus toast lama jika ada
            const existingToast = document.getElementById('custom-toast');
            if (existingToast) existingToast.remove();

            // Buat Container Toast
            const toast = document.createElement('div');
            toast.id = 'custom-toast';

            // Warna Berdasarkan Tipe (Filament Style)
            const bgColor = type === 'danger' ? '#ef4444' : '#22c55e';
            const icon = type === 'danger' ?
                '<svg style="width:20px;height:20px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
                '<svg style="width:20px;height:20px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 m-2 4l10-10"></path></svg>';

            // CSS Styling Langsung di JS
            Object.assign(toast.style, {
                position: 'fixed',
                top: '20px',
                right: '20px',
                padding: '12px 20px',
                borderRadius: '8px',
                backgroundColor: '#1f2937', // Dark mode background
                color: '#fff',
                boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
                zIndex: '10000',
                display: 'flex',
                alignItems: 'center',
                gap: '12px',
                borderLeft: `5px solid ${bgColor}`,
                fontFamily: 'sans-serif',
                fontSize: '14px',
                transition: 'all 0.3s ease',
                transform: 'translateY(-20px)',
                opacity: '0'
            });

            toast.innerHTML = `${icon} <span>${message}</span>`;
            document.body.appendChild(toast);

            // Animasi Masuk
            setTimeout(() => {
                toast.style.transform = 'translateY(0)';
                toast.style.opacity = '1';
            }, 10);

            // Hilang Otomatis setelah 4 detik
            setTimeout(() => {
                toast.style.transform = 'translateY(-20px)';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        };

        document.addEventListener('alpine:init', () => {
            Alpine.data('deliveryApp', () => ({
                // Deteksi ID dari PHP atau dari URL secara langsung
                isEdit: {{ isset($recordId) && $recordId ? 'true' : 'false' }},
                recordId: {{ $recordId ?? 'null' }},
                piSelected: false,
                loading: false,

                searchPi: '',
                allPis: @json($pis), // Mengoper data PHP ke JS

                // Getter untuk filter pencarian
                get filteredPis() {
                    if (!this.searchPi) return this.allPis;
                    return this.allPis.filter(pi =>
                        pi.number.toLowerCase().includes(this.searchPi.toLowerCase()) ||
                        pi.customer.toLowerCase().includes(this.searchPi.toLowerCase())
                    );
                },

                formData: {
                    proforma_invoice_id: '',
                    pi_number: '',
                    customer_name: '',
                    brand_name: '',
                    no_sj: '',
                    notes: '', // <-- Tambahkan ini
                    status: 'draft',
                    items: []
                },

                async init() {
                    // Jika ada recordId, paksa jalankan fetchDetail
                    if (this.recordId) {
                        console.log("Mode Edit Terdeteksi untuk ID:", this.recordId);
                        await this.fetchDeliveryDetail();
                    }
                },

                async handlePiSelection(id) {
                    if (!id) return;
                    this.loading = true;
                    try {
                        const res = await fetch(`/api/delivery/pi-detail/${id}`);
                        if (!res.ok) throw new Error('Gagal mengambil detail PI');
                        const data = await res.json();

                        // Mapping data header dari API ke Alpine State
                        this.formData.proforma_invoice_id = id;
                        this.formData.pi_number = data.pi_number;
                        this.formData.customer_id = data.customer_id;
                        this.formData.customer_name = data.customer_name;
                        this.formData.brand_name = data.brand_name;
                        this.formData.nama_cabang = data.nama_cabang; // Tangkap data cabang



                        // --- LOGIKA GENERATE NOMOR SJ UNTUK TAMPILAN (INFO SAJA) ---
                        const now = new Date();
                        const year = now.getFullYear().toString().slice(-2); // 26
                        const month = (now.getMonth() + 1).toString().padStart(2, '0'); // 02
                        const date = now.getDate().toString().padStart(2, '0'); // 21

                        // Karena kita tidak tahu counter pastinya sebelum simpan ke DB, 
                        // kita tampilkan formatnya dengan tanda bantu [AUTO] atau counter sementara
                        this.formData.no_sj = `SJ${year}${month}${date}XXXX`;

                        // Mapping items
                        this.formData.items = data.items
                            // 1. Filter dulu: Hanya ambil yang sisa kirimnya lebih dari 0
                            .filter(i => i.qty_sisa_kirim > 0)
                            // 2. Baru kemudian di-mapping ke format yang diinginkan
                            .map(i => ({
                                proforma_invoice_item_id: i.pi_item_id,
                                product_name: i.product_name,
                                unit_price: i.unit_price,
                                qty_order: i.qty_order,
                                qty_already_shipped: i.qty_already_shipped,
                                qty_sisa_kirim: i.qty_sisa_kirim,
                                qty_shipped: i.qty_sisa_kirim, // Auto-fill semua sisa
                                qty_received_good: i.qty_sisa_kirim,
                                qty_wasted: 0,
                                product_id: i.product_id,
                                warehouse_id: '',
                                stock_available: 0,
                            }));

                        this.piSelected = true;
                    } catch (e) {
                        alert(e.message);
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchDeliveryDetail() {
                    this.loading = true;
                    try {
                        const res = await fetch(`/api/delivery/${this.recordId}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (!res.ok) throw new Error('Gagal mengambil data dari server');
                        const data = await res.json();

                        // 1. Map Header
                        this.formData.proforma_invoice_id = data.proforma_invoice_id;
                        this.formData.pi_number = data.proforma_invoice?.number || '-';
                        this.formData.no_sj = data.no_sj;
                        this.formData.status = data.status;
                        this.formData.driver_name = data.driver_name;
                        this.formData.vehicle_plate = data.vehicle_plate;
                        this.formData.delivery_date = data.delivery_date;

                        nomorSJ = data.no_sj;

                        // 2. Map Customer & Brand (Sesuai struktur JSON: customer_brand > customer_induk)
                        const custBrand = data.proforma_invoice?.customer_brand;
                        this.formData.customer_id = data.proforma_invoice?.customer_brand_id;
                        this.formData.customer_name = custBrand?.customer_induk?.name || 'N/A';
                        this.formData.brand_name = custBrand?.brand_name || '-';
                        this.formData.nama_cabang = custBrand?.nama_cabang || '-';
                        this.formData.notes = data.notes || '';

                        // 3. Map Items - Perhatikan perhitungan sisa kirim
                        this.formData.items = data.items
                            // 1. Filter dulu: Hanya ambil yang qty_sisa_kirim lebih dari 0
                            .filter(i => {
                                const sisa = parseFloat(i.qty_shipped) || 0;
                                return sisa > 0;
                            })
                            // 2. Baru kemudian di-map ke format yang diinginkan
                            .map(i => {
                                // Logika: Sisa yang boleh dikirim
                                const sisaKirim = parseFloat(i.qty_sisa_kirim) || 0;
                                const qtyShipped = parseFloat(i.qty_shipped) || 0;

                                const maxBolehKirim = sisaKirim + qtyShipped;

                                return {
                                    id: i.id,
                                    proforma_invoice_item_id: i.proforma_invoice_item_id,
                                    product_id: i.pi_item?.product_id,
                                    product_name: i.pi_item?.product?.name || 'Produk',
                                    unit_price: i.pi_item?.unit_price || 0,
                                    qty_order: i.pi_item?.qty || 0,
                                    qty_sisa_kirim: sisaKirim,
                                    qty_shipped: qtyShipped,
                                    qty_received_good: i.qty_received_good,
                                    qty_wasted: i.qty_wasted,
                                    warehouse_id: i.warehouse_id ? i.warehouse_id.toString() :
                                        '',
                                    stock_available: 0,
                                };
                            });

                        this.piSelected = true;

                    } catch (e) {
                        console.error("Error Fetch Detail:", e);
                        alert("Error: " + e.message);
                    } finally {
                        this.loading = false;
                    }
                },

                async saveData() {
                    this.loading = true;

                    // 1. Pastikan ada tanggal pengiriman (karena divalidasi di Controller)
                    if (!this.formData.delivery_date) {
                        this.formData.delivery_date = new Date().toISOString().split('T')[0];
                    }

                    let url = this.isEdit ? `/api/delivery/${this.recordId}` : '/api/delivery';

                    if (this.formData.status === 'shipping' && this.isEdit) {
                        url = `/api/delivery/${this.recordId}/complete`;
                    }

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json', // PENTING: Agar Laravel kirim error JSON, bukan redirect 302
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const result = await res.json();

                        if (res.ok) {
                            // Berhasil!
                            window.location.href =
                                "{{ \App\Filament\Resources\Deliveries\DeliveryResource::getUrl('index') }}";
                        } else {
                            // Jika validasi gagal (Error 422), munculkan pesannya
                            console.error('Validation Error:', result);
                            showToast(result.message ||
                                'Gagal menyimpan, cek kembali kuota PI.', 'danger');
                            this.loading = false;
                        }
                    } catch (e) {

                        console.error(e);
                        showToast('Terjadi kesalahan sistem atau koneksi putus.', 'danger');
                        this.loading = false;
                    }
                },



                async fetchItemStock(item) {
                    if (!item.warehouse_id || !item.product_id) return;

                    try {
                        // Sesuaikan URL API dengan route di backend Anda
                        const res = await fetch(
                            `/api/inventory/check-stock?warehouse_id=${item.warehouse_id}&product_id=${item.product_id}`
                        );
                        const data = await res.json();

                        // Asumsi API mengembalikan { stock: 100 }
                        item.stock_available = data.stock || 0;

                        if (!this.isEdit) {
                            this.validateQty(item);
                        }

                    } catch (e) {
                        console.error("Gagal ambil stok", e);
                    }
                },

                validateQty(item) {
                    let qty = parseInt(item.qty_shipped) || 0;

                    // Validasi 1: Tidak boleh melebihi sisa piutang
                    if (qty > item.qty_sisa_kirim) {
                        // alert('Qty kirim tidak boleh melebihi sisa piutang!');
                        // item.qty_shipped = item.qty_sisa_kirim;
                    }

                    // Validasi 2: Tidak boleh melebihi stok gudang (Jika gudang sudah dipilih)
                    // if (item.warehouse_id && qty > item.stock_available) {
                    //     alert(`Stok di gudang tidak mencukupi! (Tersedia: ${item.stock_available})`);
                    //     item.qty_shipped = item.stock_available;
                    // }

                    // gudang harus di pilih dulu sebelum validasi stok
                }
            }));
        });

        window.addEventListener('trigger-print', e => {
            const frame = document.getElementById('print-frame');
            if (!frame) return;

            console.log("Mencoba mencetak dari URL:", e.detail.url);

            console.log("Nomor SJ untuk judul:", nomorSJ);

            document.title = "Surat Jalan - " + nomorSJ;

            frame.src = e.detail.url;
            frame.onload = () => {
                try {
                    frame.contentWindow.focus();
                    frame.contentWindow.print();
                } catch (error) {
                    console.error("Gagal mencetak melalui iframe:", error);
                    // Fallback jika iframe gagal
                    window.open(e.detail.url, '_blank');
                }
            };
        });
    </script>

    <iframe id="print-frame" style="display:none;"></iframe>

    <script></script>

    @php
        // Mengambil ID dari URL: /erp/deliveries/{id}/edit
        $recordId = request()->route('record');
    @endphp
</x-filament-panels::page>
