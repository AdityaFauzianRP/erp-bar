<x-filament-panels::page>
    <style>
        .erp-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 10px;
        }

        .erp-table th {
            background: #1e40af;
            /* Warna biru biar kontras */
            padding: 12px 15px;
            color: white;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 1px;
            text-align: left;
        }

        .erp-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .qty-column {
            width: 120px;
            /* Memberi ruang agar angka tidak menempel */
            text-align: center !important;
        }

        .erp-container {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }

        .erp-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .erp-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f5f9;
        }

        .erp-header h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .erp-icon {
            color: #2563eb;
            width: 24px;
            height: 24px;
        }

        .erp-grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .doc-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .btn-doc {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            transition: 0.3s;
            border: 1px solid #e2e8f0;
            background: white;
            color: #475569;
        }

        .btn-doc:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }

        .erp-grid-footer {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 30px;
            align-items: center;
        }

        .total-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #bfdbfe;
            padding: 30px;
            border-radius: 20px;
        }

        .total-amount {
            color: #1e40af;
            font-size: 2.5rem;
            font-weight: 900;
            display: block;
        }

        .erp-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            padding: 20px 0;
        }

        .btn-custom {
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 0.95rem;
        }

        .btn-save {
            background: #2563eb;
            color: white;
            transition: all 0.2s;
        }

        .btn-save:hover:not(:disabled) {
            background: #1d4ed8;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #64748b;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        /* State Loading Khusus */
        .btn-loading-state {
            opacity: 0.6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
            filter: grayscale(0.5);
        }

        @media (max-width: 768px) {
            .erp-grid-footer {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php
        $state = $this->form->getRawState();
        $totalTagihan = $state['total_akhir'] ?? 0;

        $transaksi = $this->record;
        $pId = $transaksi->purchase_id ?? null;

        // CEK APAKAH INI PO-DIRECT
        // Kita anggap kategori 'PO' adalah Direct (sesuai settingan tombol edit sebelumnya)
        $isDirect = ($state['kategori'] ?? '') === 'PO-DIRECT';

        $purchaseRecord = $pId ? \DB::table('purchases')->where('id', $pId)->first() : null;

        $orderData = collect();
        if ($pId) {
            $orderData = \DB::table('purchase_items')
                ->join('products', 'purchase_items.product_id', '=', 'products.id')
                ->where('purchase_items.purchase_id', $pId)
                ->select(
                    'purchase_items.id as pi_id',
                    'purchase_items.quantity as qty_order',
                    'purchase_items.unit_price',
                    'products.name as product_name',
                    'products.code as product_sku',
                )
                ->get();

            // 3. Ambil total penerimaan HANYA JIKA BUKAN Direct Purchase
            $receivingSummary = collect();
            if (!$isDirect) {
                $receivingSummary = \DB::table('receiving_items')
                    ->join('receiving_reports', 'receiving_items.receiving_report_id', '=', 'receiving_reports.id')
                    ->where('receiving_reports.purchase_id', $pId)
                    ->select(
                        'receiving_items.purchase_item_id',
                        \DB::raw('SUM(qty_received) as total_received'),
                        \DB::raw('SUM(qty_rejected) as total_rejected'),
                    )
                    ->groupBy('receiving_items.purchase_item_id')
                    ->get()
                    ->keyBy('purchase_item_id');
            }
        }

        $isLunas = in_array($state['status_bayar'] ?? '', ['Terbayar', 'Lunas']);
        $isPO = in_array($state['kategori'] ?? '', ['PO', 'Pre-Order']);
        $isPI = in_array($state['kategori'] ?? '', ['PI', 'Proforma Invoice']);
    @endphp
    <div class="erp-container">
        <form wire:submit.prevent="{{ str_contains(Route::currentRouteName(), 'create') ? 'create' : 'save' }}">
            <div class="erp-card">
                <div class="erp-header">
                    <div class="erp-icon"><x-heroicon-m-identification /></div>
                    <h2>Informasi Utama</h2>
                </div>
                <div class="erp-grid-3">
                    {{ $this->form->getComponent('nomor_transaksi') }}
                    {{ $this->form->getComponent('created_at_display') }}
                    {{ $this->form->getComponent('tanggal_transaksi') }}
                    {{-- {{ $this->form->getComponent('kategori') }} --}}
                </div>
            </div>

            <div class="erp-card">
                <div class="erp-header">
                    <div class="erp-icon"><x-heroicon-m-shopping-cart /></div>
                    <h2>Detail Pesanan & Penerimaan</h2>
                </div>

                <div style="overflow-x: auto;">
                    <table class="erp-table">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Item Barang</th>
                                <th>Qty Order</th>
                                <th>Harga Satuan</th>
                                <th>Diterima</th>
                                <th>Reject</th>
                                <th>Total Harga</th>
                            </tr>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orderData as $item)
                                @php
                                    if ($isDirect) {
                                        // JIKA DIRECT: Qty Diterima = Qty Order, Reject = 0
                                        $qtyReceived = $item->qty_order;
                                        $qtyRejected = 0;
                                    } else {
                                        // JIKA REGULER: Ambil dari hasil join receiving_items
                                        $summary = $receivingSummary->get($item->pi_id);
                                        $qtyReceived = $summary->total_received ?? 0;
                                        $qtyRejected = $summary->total_rejected ?? 0;
                                    }

                                    $subtotal = $item->qty_order * ($item->unit_price ?? 0);
                                @endphp
                                <tr>
                                    <td><span style="font-weight: bold;">{{ $item->product_sku ?? '-' }}</span></td>
                                    <td><span style="font-weight: bold;">{{ $item->product_name }}</span></td>

                                    <td>
                                        <span class="qty-badge"
                                            style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 6px; font-weight: bold;">
                                            {{ (float) $item->qty_order }}
                                        </span>
                                    </td>

                                    <td style="color: #1e293b; font-weight: 500;">
                                        Rp {{ number_format($item->unit_price ?? 0, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        <span class="qty-badge"
                                            style="background: #ecfdf5; color: #059669; padding: 4px 8px; border-radius: 6px; font-weight: bold;">
                                            {{ (float) $qtyReceived }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="qty-badge"
                                            style="background: #fff1f2; color: #e11d48; padding: 4px 8px; border-radius: 6px; font-weight: bold;">
                                            {{ (float) $qtyRejected }}
                                        </span>
                                    </td>

                                    <td style="font-weight: bold; color: #1e40af;">
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div
                    style="margin-top: 20px; padding: 15px; background: #fffbeb; border-radius: 12px; border: 1px solid #fef3c7;">
                    <h4
                        style="font-size: 11px; font-weight: 800; color: #92400e; text-transform: uppercase; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                        <x-heroicon-m-chat-bubble-bottom-center-text style="width: 14px;" /> Catatan Pembelian (PO)
                    </h4>
                    <p style="font-size: 13px; color: #b45309; margin: 0;">
                        {{ $purchaseRecord?->notes ?? 'Tidak ada catatan khusus pada pembelian ini.' }}
                    </p>
                </div>
            </div>



            <div class="erp-card">
                <div class="erp-grid-footer">
                    <div class="total-box">
                        <span style="font-size: 0.75rem; font-weight: 800; color: #1d4ed8; letter-spacing: 1.5px;">TOTAL
                            TAGIHAN</span>
                        <div class="total-amount"> Rp. {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                        <div style="margin-top: 15px;">{{ $this->form->getComponent('status_bayar') }}</div>
                    </div>
                    <div class="upload-area">{{ $this->form->getComponent('image') }}</div>
                </div>
            </div>

            <div class="erp-card">
                <div class="erp-header">
                    <div class="erp-icon"><x-heroicon-m-credit-card /></div>
                    <h2>Metode Pembayaran</h2>
                </div>
                <div class="erp-grid-3">
                    {{ $this->form->getComponent('metode_pembayaran') }}
                    {{ $this->form->getComponent('bank_account_id') }}
                    {{ $this->form->getComponent('tenggat_waktu') }}
                </div>
            </div>

            @if (!str_contains(Route::currentRouteName(), 'view'))
                <div class="erp-actions">
                    <a href="{{ static::$resource::getUrl('index') }}" class="btn-custom"
                        style="background: #dc2626; color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        <x-heroicon-m-x-circle style="width:18px;" /> Batal
                    </a>

                    {{-- <button type="button" class="btn-custom"
                        style="background: #0bf51f; color: white; display: flex; align-items: center; gap: 8px;">
                        <x-heroicon-o-printer style="width:18px;" /> BUKTI BAYAR (PDF)
                    </button> --}}

                    @if ($isPO)
                        <button type="button" onclick="printPO()" class="btn-custom"
                            style="background: #f59e0b; color: white; display: flex; align-items: center; gap: 8px;">
                            <x-heroicon-o-document-arrow-down style="width:18px;" /> Print PO
                        </button>
                    @endif

                    @if ($isDirect)
                        <button type="button" onclick="printPO()" class="btn-custom"
                            style="background: #f59e0b; color: white; display: flex; align-items: center; gap: 8px;">
                            <x-heroicon-o-document-arrow-down style="width:18px;" /> Print PO
                        </button>
                    @endif

                    <button type="submit" id="btn-simpan-transaksi" class="btn-custom btn-save"
                        @if ($isLunas) disabled style="opacity:0.5; cursor:not-allowed;" @endif>
                        <span id="text-normal" style="display: flex !important; align-items: center; gap: 8px;">
                            <x-heroicon-m-check-circle style="width:18px;" />
                            {{ $isLunas ? 'Lunas (Terkunci)' : 'Simpan Transaksi' }}
                        </span>
                        <span id="text-loading" style="display: none !important; align-items: center; gap: 8px;">
                            <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            PROSES...
                        </span>
                    </button>
                </div>
            @endif
        </form>
    </div>

    <iframe id="print_frame" name="print_frame" style="display:none;"></iframe>

    <script>
        function printPO() {
            const frame = document.getElementById('print_frame');
            frame.src = "{{ route('purchase.print', $this->record->id ?? 0) }}";
            frame.onload = function() {
                frame.contentWindow.focus();
                frame.contentWindow.print();
            };
        }

        document.addEventListener('livewire:init', () => {
            const btn = document.getElementById('btn-simpan-transaksi');
            const textNormal = document.getElementById('text-normal');
            const textLoading = document.getElementById('text-loading');
            const isLunasInitial = {{ $isLunas ? 'true' : 'false' }};

            if (!btn) return;

            // Fungsi saklar tombol
            const setBtnState = (isUploading) => {
                if (isUploading) {
                    btn.classList.add('btn-loading-state');
                    btn.disabled = true;
                    btn.style.pointerEvents = 'none'; // Kunci klik fisik
                    textNormal.style.setProperty('display', 'none', 'important');
                    textLoading.style.setProperty('display', 'flex', 'important');
                } else {
                    // Pastikan tidak ada aktivitas upload lain sebelum membuka kunci
                    btn.classList.remove('btn-loading-state');
                    btn.disabled = isLunasInitial;
                    btn.style.pointerEvents = isLunasInitial ? 'none' : 'auto';
                    textNormal.style.setProperty('display', 'flex', 'important');
                    textLoading.style.setProperty('display', 'none', 'important');
                }
            };

            // 1. Tangkap Event Global Livewire (Fase Server)
            window.addEventListener('livewire-upload-start', () => setBtnState(true));
            window.addEventListener('livewire-upload-finish', () => setBtnState(false));
            window.addEventListener('livewire-upload-error', () => setBtnState(false));

            // 2. Tangkap Event File Upload (Fase Persentase/Browser)
            // Filament memicu event ini saat komponen file upload bekerja
            window.addEventListener('file-upload-started', () => setBtnState(true));
            window.addEventListener('file-upload-finished', () => setBtnState(false));

            // 3. Monitor Perubahan Input Secara Langsung (Fase Klik Pilih File)
            const observeInputs = () => {
                document.querySelectorAll('input[type="file"]').forEach(input => {
                    if (!input.dataset.monitored) {
                        input.addEventListener('change', (e) => {
                            if (e.target.files.length > 0) {
                                setBtnState(true);
                            }
                        });
                        input.dataset.monitored = "true";
                    }
                });
            };

            const observer = new MutationObserver(observeInputs);
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
            observeInputs();

            // 4. Sinkronisasi dengan Hook Livewire (Fase Commit)
            Livewire.hook('commit', ({
                component
            }) => {
                const data = Livewire.find(component.id);
                // Jika Livewire melaporkan ada proses upload, kunci tombol!
                if (data && data.__instance && data.__instance.ephemeral && data.__instance.ephemeral
                    .uploading) {
                    setBtnState(true);
                }
            });
        });
    </script>
</x-filament-panels::page>
