<x-filament-panels::page>
    <style>
        /* =======================================================
           THEME: Light (White + Blue), Scoped agar aman untuk Filament
           ======================================================= */
        #combined-editor {
            --blue-50: #eff6ff;
            --blue-100: #dbeafe;
            --blue-200: #bfdbfe;
            --blue-300: #93c5fd;
            --blue-400: #60a5fa;
            --blue-500: #3b82f6;
            --blue-600: #2563eb;

            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-900: #0f172a;

            --success-50: #ecfdf5;
            --success-200: #bbf7d0;
            --success-600: #16a34a;
            --warn-50: #fffbeb;
            --warn-200: #fde68a;
            --warn-700: #92400e;
            --danger-50: #fef2f2;
            --danger-200: #fecaca;
            --danger-700: #991b1b;

            --radius-lg: 12px;
            --radius-md: 10px;
            --radius-sm: 8px;
        }

        /* Card & header */
        #combined-editor .ce-card {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            box-shadow: 0 8px 22px rgba(0, 0, 0, .06);
            overflow: hidden;
            margin-bottom: 12px;
        }

        #combined-editor .ce-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-bottom: 1px solid var(--blue-100);
            background: linear-gradient(180deg, #f6f9ff, #eef4ff);
            color: var(--slate-900);
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .2px;
        }

        #combined-editor .ce-body {
            padding: 16px;
        }

        #combined-editor .ce-actions {
            position: sticky;
            bottom: 0;
            z-index: 1;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            padding: 12px 16px;
            border-top: 1px solid var(--blue-100);
            background: linear-gradient(180deg, #f9fbff, #f3f7ff);
        }

        /* Grid util */
        #combined-editor .ce-row {
            display: grid;
            gap: 12px;
            margin-bottom: 12px;
            grid-template-columns: repeat(12, 1fr);
        }

        #combined-editor .ce-col-12 {
            grid-column: span 12;
        }

        .ce-col-8 {
            grid-column: span 8;
        }

        #combined-editor .ce-col-6 {
            grid-column: span 6;
        }

        .ce-col-4 {
            grid-column: span 4;
        }

        .ce-col-3 {
            grid-column: span 3;
        }

        @media (max-width:1024px) {

            #combined-editor .ce-col-8,
            .ce-col-6,
            .ce-col-4,
            .ce-col-3 {
                grid-column: span 12;
            }
        }

        /* Inputs */
        #combined-editor label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            color: var(--slate-600);
        }

        #combined-editor input[type="text"],
        #combined-editor input[type="date"],
        #combined-editor select,
        #combined-editor textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--slate-300);
            background: #fff;
            color: var(--slate-900);
            outline: none;
            font-size: 14px;
            transition: border-color .15s, box-shadow .15s, background-color .15s;
        }

        #combined-editor textarea {
            min-height: 90px;
            resize: vertical;
        }

        #combined-editor input:focus,
        #combined-editor select:focus,
        #combined-editor textarea:focus {
            border-color: var(--blue-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .18);
        }

        #combined-editor input[readonly],
        #combined-editor textarea[readonly],
        #combined-editor select[disabled] {
            background: var(--slate-50);
            color: var(--slate-700);
            cursor: not-allowed;
        }

        #combined-editor .ce-help {
            font-size: 12px;
            color: var(--slate-500);
            margin-top: 4px;
        }

        /* Chip & alert */
        #combined-editor .ce-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid var(--blue-200);
            background: var(--blue-50);
            color: #1e40af;
            font-size: 12px;
        }

        #combined-editor .ce-chip.green {
            border-color: var(--success-200);
            background: var(--success-50);
            color: var(--success-600);
        }

        #combined-editor .ce-chip.yellow {
            border-color: var(--warn-200);
            background: var(--warn-50);
            color: var(--warn-700);
        }

        #combined-editor .ce-chip.red {
            border-color: var(--danger-200);
            background: var(--danger-50);
            color: var(--danger-700);
        }

        #combined-editor .ce-alert {
            display: none;
            margin-bottom: 12px;
            padding: 12px 14px;
            border-radius: var(--radius-md);
            border: 1px solid;
            font-size: 13px;
        }

        #combined-editor .ce-alert.show {
            display: block;
        }

        #combined-editor .ce-alert.info {
            background: var(--blue-50);
            border-color: var(--blue-200);
            color: #1e3a8a;
        }

        #combined-editor .ce-alert.warn {
            background: var(--warn-50);
            border-color: var(--warn-200);
            color: var(--warn-700);
        }

        #combined-editor .ce-alert.danger {
            background: var(--danger-50);
            border-color: var(--danger-200);
            color: var(--danger-700);
        }

        #combined-editor .code {
            font-family: ui-monospace, Menlo, Consolas, monospace;
            padding: 1px 6px;
            border-radius: 6px;
            background: var(--slate-100);
            border: 1px solid var(--slate-200);
        }

        /* Buttons */
        #combined-editor .btn {
            appearance: none;
            border: 1px solid var(--blue-500);
            background: linear-gradient(180deg, var(--blue-400), var(--blue-500));
            color: #fff;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: transform .06s ease, box-shadow .2s;
        }

        #combined-editor .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, .28);
        }

        #combined-editor .btn:disabled {
            opacity: .6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        #combined-editor .btn.text {
            background: transparent;
            border-color: transparent;
            color: var(--blue-600);
            padding: 8px 6px;
        }

        /* Spinner */
        #combined-editor .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(59, 130, 246, .25);
            border-top-color: var(--blue-600);
            border-radius: 50%;
            animation: ceSpin .8s linear infinite;
            display: inline-block;
            vertical-align: -3px;
            margin-left: 8px;
        }

        @keyframes ceSpin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Table */
        #combined-editor .ce-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--slate-200);
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        #combined-editor .ce-table th,
        #combined-editor .ce-table td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--slate-200);
            text-align: left;
            font-size: 13px;
            color: var(--slate-900);
        }

        #combined-editor .ce-table th {
            background: var(--slate-50);
            color: var(--slate-600);
            font-weight: 700;
        }

        #combined-editor .ce-table tr:last-child td {
            border-bottom: none;
        }

        #combined-editor .num {
            text-align: right;
            white-space: nowrap;
        }

        @media (max-width:760px) {
            #combined-editor .ce-table thead {
                display: none;
            }

            #combined-editor .ce-table,
            #combined-editor .ce-table tbody,
            #combined-editor .ce-table tr,
            #combined-editor .ce-table td {
                display: block;
                width: 100%;
            }

            #combined-editor .ce-table tr {
                border-bottom: 1px solid var(--slate-200);
                margin-bottom: 8px;
                border-radius: 8px;
                background: #fff;
            }

            #combined-editor .ce-table td {
                border: none;
                display: grid;
                grid-template-columns: 1fr 1fr;
                padding: 8px 12px;
            }

            #combined-editor .ce-table td::before {
                content: attr(data-label);
                color: var(--slate-500);
                font-weight: 600;
                padding-right: 10px;
                text-align: left;
            }

            #combined-editor .num {
                text-align: right;
            }
        }

        /* Skeleton & topbar */
        #combined-editor .skeleton {
            width: 100%;
            height: 14px;
            border-radius: 6px;
            background: linear-gradient(90deg, var(--slate-100), var(--slate-200), var(--slate-100));
            background-size: 200% 100%;
            animation: ceShimmer 1.2s infinite;
        }

        @keyframes ceShimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        #combined-editor .topbar {
            height: 3px;
            width: 0;
            background: linear-gradient(90deg, var(--blue-300), var(--blue-600));
            transition: width .25s ease;
            border-radius: 0 3px 3px 0;
        }

        #combined-editor .topbar.active {
            width: 100%;
        }

        /* Uploader (Dropzone + Progress + Preview) */
        #combined-editor .uploader {
            position: relative;
            padding: 16px;
            border: 2px dashed var(--blue-300);
            border-radius: var(--radius-md);
            background: linear-gradient(180deg, var(--blue-50), #ffffff);
            transition: border-color .2s, box-shadow .2s, background .3s;
        }

        #combined-editor .uploader:hover {
            border-color: var(--blue-500);
            box-shadow: 0 6px 20px rgba(59, 130, 246, .15);
        }

        #combined-editor .uploader.dragover {
            border-color: var(--blue-600);
            box-shadow: 0 8px 24px rgba(37, 99, 235, .22);
            background: linear-gradient(180deg, #f0f6ff, #ffffff);
        }

        #combined-editor .uploader .up-row {
            display: flex;
            gap: 14px;
            align-items: center;
            flex-wrap: wrap;
        }

        #combined-editor .uploader .up-icon {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at 50% 35%, var(--blue-100), var(--blue-50));
            border: 1px solid var(--blue-200);
            animation: pulseCloud 2s infinite ease-in-out;
        }

        @keyframes pulseCloud {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, .28);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(59, 130, 246, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }

        #combined-editor .uploader .up-text {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        #combined-editor .uploader .up-title {
            font-weight: 700;
            color: var(--slate-900);
            font-size: 14px;
        }

        #combined-editor .uploader .up-sub {
            color: var(--slate-600);
            font-size: 12px;
        }

        #combined-editor .uploader .up-actions {
            margin-left: auto;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        #combined-editor .uploader .up-btn {
            background: #fff;
            border: 1px solid var(--blue-300);
            color: var(--blue-600);
            padding: 8px 12px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            transition: transform .06s, box-shadow .2s;
        }

        #combined-editor .uploader .up-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(59, 130, 246, .18);
        }

        #combined-editor .progress {
            margin-top: 10px;
            width: 100%;
            height: 10px;
            border-radius: 999px;
            background: var(--slate-100);
            overflow: hidden;
            border: 1px solid var(--slate-200);
            display: none;
        }

        #combined-editor .progress .bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--blue-400), var(--blue-600));
            transition: width .2s ease;
        }

        #combined-editor .progress .label {
            margin-top: 6px;
            font-size: 12px;
            color: var(--slate-600);
        }

        #combined-editor .success {
            display: none;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            color: var(--success-600);
            font-weight: 700;
            font-size: 13px;
        }

        #combined-editor .check {
            width: 22px;
            height: 22px;
        }

        #combined-editor .check circle {
            fill: none;
            stroke: var(--success-600);
            stroke-width: 2;
            opacity: .2;
        }

        #combined-editor .check path {
            fill: none;
            stroke: var(--success-600);
            stroke-width: 3;
            stroke-dasharray: 50;
            stroke-dashoffset: 50;
            animation: drawCheck .6s ease forwards;
        }

        @keyframes drawCheck {
            to {
                stroke-dashoffset: 0;
            }
        }

        #combined-editor .proof-preview {
            display: none;
            margin-top: 8px;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-sm);
            padding: 8px;
            background: var(--slate-50);
        }

        #combined-editor .proof-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        #combined-editor .proof-thumb {
            width: 84px;
            height: 84px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--slate-200);
            background: #fff;
            opacity: 0;
            transform: scale(.98);
            transition: opacity .25s ease, transform .25s ease;
        }

        #combined-editor .proof-thumb.visible {
            opacity: 1;
            transform: scale(1);
        }

        #combined-editor .proof-file {
            font-size: 13px;
            color: var(--slate-700);
        }

        #combined-editor .hidden {
            display: none !important;
        }
    </style>

    <div id="combined-editor" class="space-y-4" aria-live="polite">
        <div id="ce-topbar" class="topbar" role="status" aria-label="Memuat..."></div>

        <div id="ce-global-alert" class="ce-alert info" role="alert"></div>

        <div class="ce-card" id="card-payment">
            <div class="ce-header">Pembayaran (Editable)</div>
            <div class="ce-body">
                <div id="ce-pay-alert" class="ce-alert warn" style="display:none;">Metode <b>TRANSFER</b> memerlukan
                    pemilihan bank.</div>

                <div class="ce-row">
                    <div class="ce-col-4">
                        <label for="ce-pay-method">Metode</label>
                        <select id="ce-pay-method" aria-label="Metode Pembayaran">
                            <option value="">— Pilih —</option>
                            <option value="TRANSFER">Transfer</option>
                            <option value="CASH">Cash</option>
                        </select>
                        <div class="ce-help">Pilih cara pembayaran.</div>
                    </div>

                    <div class="ce-col-4" id="ce-bank-col">
                        <label for="ce-bank">Rekening Bank</label>
                        <select id="ce-bank" aria-label="Rekening Bank">
                            <option value="">— Pilih —</option>
                        </select>
                        <div class="ce-help">Wajib diisi bila metode <b>Transfer</b>.</div>
                    </div>

                    <div class="ce-col-4">
                        <label>Bukti Pembayaran (jpg/png/jpeg)</label>

                        <div id="ce-uploader" class="uploader" tabindex="0" role="button"
                            aria-label="Unggah bukti pembayaran (klik atau tarik & lepas)">
                            <div class="up-row">
                                <div class="up-icon" aria-hidden="true">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M7 18a4 4 0 0 1 0-8c.2 0 .4 0 .6.1A5 5 0 0 1 17 8a4 4 0 0 1 1 7.9"
                                            stroke="#2563eb" stroke-width="1.8" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M12 13v6m0 0l-3-3m3 3l3-3" stroke="#2563eb" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div class="up-text">
                                    <div class="up-title">Tarik & lepas file ke sini</div>
                                    <div class="up-sub">atau <strong>klik</strong> untuk memilih file dari komputer
                                    </div>
                                </div>
                                <div class="up-actions">
                                    <button type="button" id="ce-btn-browse" class="up-btn">Pilih File</button>
                                </div>
                            </div>
                            <input id="ce-proof" type="file" accept=".jpg,.jpeg,.png" class="hidden" />
                        </div>

                        <div id="ce-proof-existing" class="ce-help" style="display:none; margin-top:8px;"></div>

                        <div id="ce-proof-preview" class="proof-preview" aria-live="polite">
                            <div class="proof-row">
                                <img id="ce-proof-thumb" class="proof-thumb hidden" alt="Preview bukti">
                                <div id="ce-proof-fileinfo" class="proof-file"></div>
                            </div>
                        </div>

                        <div id="ce-progress" class="progress" aria-label="Progres unggahan">
                            <div id="ce-progress-bar" class="bar"></div>
                            <div id="ce-progress-label" class="label">Mengunggah… 0%</div>
                        </div>

                        <div id="ce-success" class="success" aria-live="polite">
                            <svg class="check" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M6 12l4 4 8-8"></path>
                            </svg>
                            <span>Upload selesai</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ce-actions">
                <button id="ce-save" class="btn" type="button">
                    Simpan Perubahan Pembayaran
                    <span id="ce-save-spin" class="spinner" style="display:none;"></span>
                </button>
                <button type="button" onclick="printInvoice()" class="btn-print"
                    style="background: #000000; border-color: transparent; color: white; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    🖨️ Cetak Tukar Faktur
                </button>
            </div>
        </div>

        <div class="ce-card" id="card-info">
            <div class="ce-header">
                <div>Informasi Invoice Gabungan</div>
                <div id="ce-status-chip" class="ce-chip">Status: —</div>
            </div>
            <div class="ce-body">
                <div class="ce-row">
                    <div class="ce-col-4">
                        <label for="ce-number">Nomor Gabungan</label>
                        <input id="ce-number" type="text" readonly />
                    </div>
                    <div class="ce-col-4">
                        <label for="ce-issue">Tanggal Terbit</label>
                        <input id="ce-issue" type="date" readonly />
                    </div>
                    <div class="ce-col-4">
                        <label for="ce-due">Jatuh Tempo</label>
                        <input id="ce-due" type="date" readonly />
                    </div>
                </div>

                <div class="ce-row">
                    <div class="ce-col-4">
                        <label for="ce-status">Status Pembayaran</label>
                        <select id="ce-status" disabled>
                            <option value="unpaid">Belum Lunas</option>
                            <option value="partial">Sebagian</option>
                            <option value="paid">Lunas</option>
                        </select>
                    </div>
                    <div class="ce-col-4">
                        <label for="ce-cust-name">Nama Customer</label>
                        <input id="ce-cust-name" type="text" readonly />
                    </div>
                    <div class="ce-col-4">
                        <label for="ce-cust-addr">Alamat Customer</label>
                        <input id="ce-cust-addr" type="text" readonly />
                    </div>
                </div>

                <div class="ce-row">
                    <div class="ce-col-12">
                        <label for="ce-notes">Catatan</label>
                        <textarea id="ce-notes" readonly></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="ce-card" id="card-invoices">
            <div class="ce-header">Daftar Invoice</div>
            <div class="ce-body">
                <div style="overflow:auto;">
                    <table class="ce-table" id="ce-invoices-table" aria-label="Daftar Invoice">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer / Brands</th>
                                <th>No. Invoice</th>
                                <th>No. Penjualan</th>
                                <th>No. Surat Jalan</th>
                                <th>Tgl Kirim</th>
                                <th class="num">Subtotal</th>
                                <th class="num">PPN</th>
                                <th class="num">Total</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody id="ce-invoices-body">
                            <tr>
                                <td colspan="10">
                                    <div class="skeleton" style="height:28px;"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ITEMS (readonly) -->
        <div class="ce-card" id="card-items">
            <div class="ce-header">Rincian Item (Agregasi)</div>
            <div class="ce-body">
                <div class="ce-row" style="margin-top:10px;">
                    <div class="ce-col-4"><span class="ce-help">Subtotal (items)</span>
                        <div id="ce-sum-sub">Rp 0</div>
                    </div>
                    <div class="ce-col-4"><span class="ce-help">PPN (dari invoice)</span>
                        <div id="ce-sum-tax">Rp 0</div>
                    </div>
                    <div class="ce-col-4"><span class="ce-help">Total (dari invoice)</span>
                        <div id="ce-sum-total">Rp 0</div>
                    </div>
                </div>

                <div id="ce-mm-alert" class="ce-alert warn" style="margin-top:10px;">
                    Peringatan: Nilai <span class="code">financials</span> dari API tidak konsisten dengan hitungan
                    tabel.
                </div>
            </div>
        </div>


    </div>

    <script>
        const CE_API_BASE = `${location.origin}/api`;
        const CE_QS = new URLSearchParams(location.search);

        const CE_ID = "{{ $record->id ?? '' }}";

        const CE_ENDPOINT_SHOW = `${CE_API_BASE}/invoices/showCombined/${CE_ID}`; // GET data gabungan
        const CE_ENDPOINT_UPDATE =
            `${CE_API_BASE}/invoices/combined-invoices/${CE_ID}`; // PUT (1 API saja, ke controller Kang)

        const CE_TOKEN = null; // isi 'Bearer xxxxx' jika perlu auth

        /* =========================
           Helper
           ========================= */
        const $ = (id) => document.getElementById(id);
        const ceFmtIDR = n => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(Number(n || 0));
        const ceAsNum = v => v == null ? 0 : (typeof v === 'string' ? Number(v.replace(/[^\d.-]/g, '')) : Number(v));

        function ceAlert(type, html) {
            const box = $('ce-global-alert');
            box.className = `ce-alert ${type} show`;
            box.innerHTML = html;
        }

        function ceAlertClear() {
            const box = $('ce-global-alert');
            box.className = 'ce-alert info';
            box.innerHTML = '';
            box.style.display = 'none';
            setTimeout(() => box.classList.remove('show'), 0);
        }

        function ceStatusChip(val) {
            const n = $('ce-status-chip');
            n.className = 'ce-chip';
            let lbl = '—';
            if (val === 'paid') {
                n.classList.add('green');
                lbl = 'Lunas';
            } else if (val === 'partial') {
                n.classList.add('yellow');
                lbl = 'Sebagian';
            } else {
                n.classList.add('red');
                lbl = 'Belum Lunas';
            }
            n.innerHTML = `Status: <strong>${lbl}</strong>`;
        }

        function ceGuardPayment() {
            const m = $('ce-pay-method').value;
            $('ce-bank-col').style.display = (m === 'TRANSFER') ? '' : 'none';
            $('ce-pay-alert').style.display = (m === 'TRANSFER') ? 'block' : 'none';
        }

        function topbar(on) {
            $('ce-topbar').classList.toggle('active', !!on);
        }

        function setBusy(btnId, busy) {
            const btn = $(btnId),
                spin = $('ce-save-spin');
            if (!btn) return;
            btn.disabled = busy;
            spin.style.display = busy ? 'inline-block' : 'none';
        }

        function fileSizeKB(size) {
            return (size / 1024).toFixed(1) + ' KB';
        }

        // State
        let CE_ORIGINAL = null;

        /* =========================
           Render
           ========================= */
        function ceRender(data) {
            CE_ORIGINAL = structuredClone(data);

            // Header info
            $('ce-number').value = data.combined_number || '';
            $('ce-issue').value = data.issue_date || '';
            $('ce-due').value = data.due_date || '';
            $('ce-status').value = data.status_pembayaran || 'unpaid';
            ceStatusChip($('ce-status').value);
            $('ce-cust-name').value = data.customer?.name || '';
            $('ce-cust-addr').value = data.customer?.address || '';
            $('ce-notes').value = data.notes || '';

            // Payment form
            const bankSel = $('ce-bank');
            bankSel.innerHTML = `<option value="">— Pilih —</option>`;
            (data.available_banks || []).forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.textContent = `${b.bank_name} • ${b.account_number} • ${b.account_holder}`;
                bankSel.appendChild(opt);
            });

            $('ce-pay-method').value = data.payment_info?.method || '';
            if (data.payment_info?.bank_account_id) bankSel.value = String(data.payment_info.bank_account_id);

            // Existing proof tampil sebagai teks (nama file)
            const existing = $('ce-proof-existing');
            const proofUrl = data.payment_info?.proof_url || data.payment_proof || data.paymentProof || '';
            if (proofUrl) {
                existing.style.display = 'block';
                const name = (proofUrl.split('/').pop() || proofUrl);
                existing.innerHTML = `Bukti tersimpan: <span class="code">${name}</span>`;
            } else {
                existing.style.display = 'none';
                existing.innerHTML = '';
            }

            // Reset uploader UI
            $('ce-proof-preview').style.display = 'none';
            $('ce-proof-thumb').classList.add('hidden');
            $('ce-proof-thumb').classList.remove('visible');
            $('ce-proof-thumb').src = '';
            $('ce-proof-fileinfo').textContent = '';
            $('ce-proof').value = '';
            $('ce-progress').style.display = 'none';
            $('ce-progress-bar').style.width = '0%';
            $('ce-progress-label').textContent = 'Mengunggah… 0%';
            $('ce-success').style.display = 'none';

            // Invoices table
            const tbInv = $('ce-invoices-body');
            tbInv.innerHTML = '';
            (data.invoices || []).forEach((inv, i) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td data-label="#">${i+1}</td>
                    <td data-label="Customer / Brands">${inv.brand_name||'-'} - ${inv.nama_cabang||'-'}</td>
                    <td data-label="No. Invoice">${inv.invoice_number||'-'}</td>
                    <td data-label="No. Penjualan">${inv.pi_number||'-'}</td>
                    <td data-label="No. Surat Jalan">${inv.no_sj||'-'}</td>
                    <td data-label="Tgl Kirim">${inv.delivery_date||'-'}</td>
                    <td data-label="Subtotal" class="num">${ceFmtIDR(inv.subtotal)}</td>
                    <td data-label="PPN" class="num">${ceFmtIDR(inv.tax_amount)}</td>
                    <td data-label="Total" class="num"><strong>${ceFmtIDR(inv.total_amount)}</strong></td>
                    <td data-label="Lihat Detail"><a style="border: 1px solid #d1d5db; padding: 4px 10px; border-radius: 4px; color: #374151; text-decoration: none; font-size: 12px;" href="/erp/invoices/${inv.invoice_id}/edit" target="_blank" rel="noopener noreferrer">Lihat</a></td>
                    

                `;
                tbInv.appendChild(tr);
            });
            if (!(data.invoices || []).length) {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td data-label="Info" colspan="10" class="ce-help">Tidak ada invoice.</td>`;
                tbInv.appendChild(tr);
            }

            // Summary & mismatch
            const sumSub = (data.items || []).reduce((a, it) => a + ceAsNum(it.total_item), 0);
            const sumTax = (data.invoices || []).reduce((a, iv) => a + ceAsNum(iv.tax_amount), 0);
            const sumTot = (data.invoices || []).reduce((a, iv) => a + ceAsNum(iv.total_amount), 0);
            $('ce-sum-sub').textContent = ceFmtIDR(sumTot);
            $('ce-sum-tax').textContent = ceFmtIDR(sumTax);
            $('ce-sum-total').textContent = ceFmtIDR(sumTot);

            const apiSub = ceAsNum(data.financials?.subtotal);
            const apiTot = ceAsNum(data.financials?.total_amount);
            $('ce-mm-alert').style.display = ((apiSub && sumSub && apiSub !== sumSub) || (apiTot && sumTot && apiTot !==
                sumTot)) ? 'block' : 'none';

            ceGuardPayment();
        }

        async function ceFetch() {
            topbar(true);
            ceAlert('info', 'Memuat data…');
            try {
                const res = await fetch(CE_ENDPOINT_SHOW, {
                    headers: {
                        'Accept': 'application/json',
                        ...(CE_TOKEN ? {
                            'Authorization': CE_TOKEN
                        } : {})
                    },
                    credentials: 'same-origin'
                });
                if (!res.ok) throw new Error(`Gagal memuat (${res.status})`);
                const json = await res.json();
                const data = json?.data?.id ? json.data : json;
                ceRender(data);
                ceAlertClear();
            } catch (e) {
                ceAlert('danger', `Gagal memuat: <b>${e.message}</b><br><span class="code">${CE_ENDPOINT_SHOW}</span>`);
            } finally {
                topbar(false);
            }
        }

        function ceValidate() {
            const errs = [];
            if ($('ce-pay-method').value === 'TRANSFER' && !$('ce-bank').value) {
                errs.push('Metode TRANSFER memerlukan pemilihan rekening bank.');
            }
            return errs;
        }

        // Kirim 1 request PUT multipart ke controller Kang (dengan progress)
        function cePutPaymentInfo() {
            return new Promise((resolve, reject) => {
                const fd = new FormData();

                // 1. Ambil elemen input file
                const fileInput = $('ce-proof');

                // 2. Pastikan file ADA sebelum di-append
                if (fileInput.files && fileInput.files.length > 0) {
                    // Ambil file pertama [0]
                    const fileAwal = fileInput.files[0];
                    fd.append('payment_proof', fileAwal);
                    console.log("File yang akan dikirim:", fileAwal.name, fileAwal.size); // Cek di console
                }

                fd.append('payment_method', $('ce-pay-method').value);
                if ($('ce-bank').value) {
                    fd.append('bank_account_id', $('ce-bank').value);
                }

                const xhr = new XMLHttpRequest();
                xhr.open('POST', CE_ENDPOINT_UPDATE, true);

                // JANGAN setRequestHeader('Content-Type', ...) 
                // Biarkan browser yang mengisi 'multipart/form-data' beserta Boundary-nya

                if (CE_TOKEN) {
                    xhr.setRequestHeader('Authorization', CE_TOKEN);
                }
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.onload = () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve(JSON.parse(xhr.responseText));
                    } else {
                        reject(new Error(`Server Error: ${xhr.status}`));
                    }
                };

                xhr.send(fd);
            });
        }

        async function ceSave() {
            const errs = ceValidate();
            if (errs.length) {
                ceAlert('warn', 'Periksa isian:<br>• ' + errs.join('<br>• '));
                return;
            }

            setBusy('ce-save', true);
            topbar(true);
            ceAlert('info', 'Menyimpan perubahan pembayaran…');

            try {
                await cePutPaymentInfo();
                ceAlert('info', '<b>Perubahan pembayaran tersimpan.</b>');
                await ceFetch();
            } catch (e) {
                ceAlert('danger', `Kesalahan saat menyimpan: <b>${e.message}</b>`);
            } finally {
                setBusy('ce-save', false);
                topbar(false);
            }
        }

        /* =========================
           Upload UI (dropzone + preview)
           ========================= */
        function ceBindUploadUI() {
            const zone = $('ce-uploader');
            const input = $('ce-proof');
            const btnBrowse = $('ce-btn-browse');
            const preview = $('ce-proof-preview');
            const thumb = $('ce-proof-thumb');
            const info = $('ce-proof-fileinfo');

            function showPreview(file) {
                if (!file) {
                    preview.style.display = 'none';
                    thumb.classList.add('hidden');
                    thumb.classList.remove('visible');
                    thumb.src = '';
                    info.textContent = '';
                    return;
                }
                preview.style.display = 'block';
                // Hanya image (karena backend validasi image)
                const url = URL.createObjectURL(file);
                thumb.src = url;
                thumb.classList.remove('hidden');
                requestAnimationFrame(() => thumb.classList.add('visible'));
                info.textContent = `${file.name} • ${fileSizeKB(file.size)}`;
            }

            btnBrowse.addEventListener('click', () => input.click());
            zone.addEventListener('click', () => input.click());
            zone.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    input.click();
                }
            });

            // Drag & Drop
            ['dragenter', 'dragover'].forEach(evt => {
                zone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    zone.classList.add('dragover');
                });
            });
            ['dragleave', 'drop'].forEach(evt => {
                zone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    zone.classList.remove('dragover');
                });
            });
            zone.addEventListener('drop', (e) => {
                const f = e.dataTransfer?.files?.[0];
                if (!f) return;
                input.files = e.dataTransfer.files;
                showPreview(f);
                $('ce-proof-existing').style.display = 'none';
                // reset progress state
                $('ce-progress').style.display = 'none';
                $('ce-progress-bar').style.width = '0%';
                $('ce-progress-label').textContent = 'Mengunggah… 0%';
                $('ce-success').style.display = 'none';
            });

            input.addEventListener('change', () => {
                const f = input.files?.[0];
                showPreview(f);
                $('ce-proof-existing').style.display = 'none';
                $('ce-progress').style.display = 'none';
                $('ce-progress-bar').style.width = '0%';
                $('ce-progress-label').textContent = 'Mengunggah… 0%';
                $('ce-success').style.display = 'none';
            });
        }

        function ceBind() {
            $('ce-pay-method').addEventListener('change', ceGuardPayment);
            $('ce-save').addEventListener('click', ceSave);
            ceBindUploadUI();
        }

        (function ceInit() {
            ceBind();
            ceFetch();
        })();

        function printInvoice() {
            const url = "{{ route('invoice-combined.print', $record->id) }}";
            const frame = document.getElementById('print-frame');

            frame.src = url;

            const combinedNumber = document.getElementById('ce-number').value;

            document.title = "Tukar Faktur - " + combinedNumber;

            frame.onload = function() {
                // Jeda 1.5 detik agar fetch API di dalam iframe selesai
                setTimeout(() => {
                    try {
                        frame.contentWindow.focus();
                        frame.contentWindow.print();
                    } catch (e) {
                        console.error("Gagal cetak:", e);
                        window.open(url, '_blank');
                    }
                }, 1000);
            };
        }
    </script>

    <iframe id="print-frame" style="display: none;"></iframe>
</x-filament-panels::page>
