<x-filament-panels::page>
    <div class="main-component-wrapper" style="width: 100%;">
        <style>
            .custom-card {
                background: white;
                border-radius: 12px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                margin-bottom: 20px;
            }

            .card-header {
                padding: 15px 20px;
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .input-grid {
                padding: 20px;
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 15px;
            }

            .input-group {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .input-label {
                font-size: 13px;
                font-weight: 600;
                color: #64748b;
            }

            .custom-input {
                width: 100%;
                padding: 8px 12px;
                border-radius: 8px;
                border: 1px solid #cbd5e1;
                font-size: 14px;
                background: white;
            }

            .btn-save {
                background: #d97706;
                color: white;
                padding: 10px 24px;
                border-radius: 8px;
                font-weight: 700;
                border: none;
                cursor: pointer;
                transition: all 0.2s;
            }

            .btn-save:hover {
                background: #b45309;
            }

            .btn-save:disabled {
                background: #94a3b8;
                cursor: not-allowed;
            }

            .btn-print {
                background: #0f172a;
                color: white;
                padding: 10px 24px;
                border-radius: 8px;
                font-weight: 700;
                border: none;
                cursor: pointer;
            }

            .upload-container {
                position: relative;
                width: 100%;
                min-height: 45px;
                border: 2px dashed #cbd5e1;
                border-radius: 10px;
                background: #f8fafc;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                overflow: hidden;
            }

            #image-preview-container {
                position: absolute;
                inset: 0;
                background: white;
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 10;
            }

            #image-preview-container img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                padding: 5px;
            }

            .hidden {
                display: none !important;
            }

            .required-star {
                color: #ef4444;
                margin-left: 2px;
                font-weight: bold;
            }

            .inv-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            .inv-table th {
                background: #f1f5f9;
                padding: 12px;
                text-align: left;
                font-size: 11px;
                color: #475569;
                text-transform: uppercase;
                border-bottom: 2px solid #e2e8f0;
            }

            .inv-table td {
                padding: 15px 12px;
                border-bottom: 1px solid #f1f5f9;
                font-size: 14px;
                color: #1e293b;
            }

            .info-box {
                background: #f8fafc;
                padding: 15px;
                border-radius: 10px;
                border: 1px solid #f1f5f9;
            }

            .info-title {
                font-size: 11px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                margin-bottom: 8px;
                border-bottom: 1px solid #e2e8f0;
                padding-bottom: 4px;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .info-content {
                font-size: 13px;
                color: #1e293b;
                line-height: 1.5;
            }

            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }
        </style>

        <div id="action-card" class="custom-card">
            <div class="card-header">
                <span id="action-title" style="font-weight: 700; font-size: 16px;">⚙️ Aksi Konfirmasi Pembayaran</span>
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="printInvoice()" class="btn-print">
                        🖨️ Cetak Invoice
                    </button>
                    <button type="button" onclick="saveInvoiceData()" class="btn-save" id="btn-save">
                        Simpan & Konfirmasi Pembayaran
                    </button>

                    {{-- combined_status kasih kondisi --}}
                    <button type="button" onclick="cancelInvoiceCombined()" class="btn-save" id="btn-cancel">
                        Batalkan Tukar Faktur
                    </button>
                </div>
            </div>
            <div id="action-inputs" class="input-grid">
                <div class="input-group">
                    <label class="input-label">Metode Pembayaran <span class="required-star">*</span></label>
                    <select id="edit-method" class="custom-input" onchange="toggleInputs()">
                        <option value="CASH">CASH (Tunai)</option>
                        <option value="TRANSFER">TRANSFER BANK</option>
                    </select>
                </div>

                <div class="input-group" id="bank-wrapper">
                    <label class="input-label">Pilih Bank Tujuan <span class="required-star">*</span></label>
                    <select id="edit-bank" class="custom-input"></select>
                </div>

                <div class="input-group">
                    <label class="input-label">Bukti Bayar <span id="proof-status-label"
                            class="required-star"></span></label>
                    <div class="upload-container" onclick="document.getElementById('file-input').click()">
                        <div id="upload-placeholder" style="text-align: center;">
                            <p style="font-size: 11px; color: #64748b;">Klik atau Drag Gambar</p>
                        </div>
                        <div id="image-preview-container"><img id="preview-img" src=""></div>
                        <input type="file" id="file-input" class="hidden" accept="image/*"
                            onchange="previewFile(event)">
                    </div>
                </div>

                <div class="input-group">
                    <label class="input-label">Catatan Pembayaran</label>
                    <input type="text" id="edit-notes" class="custom-input"
                        placeholder="Misal: Lunas dibayar ditempat">
                </div>
            </div>
        </div>

        <div class="custom-card">
            <div id="loader" style="padding: 50px; text-align: center;">
                <svg style="width: 40px; color: #d97706; animation: spin 1s linear infinite;" fill="none"
                    viewBox="0 0 24 24">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path style="opacity: 0.75;" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <p style="margin-top: 10px; color: #64748b; font-size: 14px;">Memuat data invoice...</p>
            </div>

            <div id="invoice-content" class="hidden" style="padding: 40px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h1 id="inv-number" style="font-size: 2rem; font-weight: 900; color: #0f172a; margin: 0;"></h1>
                        <div style="margin-top: 8px;">
                            <span id="badge-method"
                                style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;"></span>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div id="bank-view" style="font-weight: 800; color: #1e293b; font-size: 1.1rem;"></div>
                        <div id="bank-acc-view" style="font-size: 13px; color: #64748b; margin-top: 4px;"></div>
                    </div>
                </div>

                <hr style="margin: 25px 0; border: 0; border-top: 1px solid #e2e8f0;">

                <div style="display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <div class="info-box">
                        <div class="info-title">👤 DITAGIHKAN KEPADA:</div>
                        <div class="info-content">
                            <div id="cust-name" style="font-weight: 800; font-size: 15px; color: #0f172a;"></div>
                            <div id="cust-address" style="margin-top: 4px; color: #475569;"></div>
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-title">📄 DATA PESANAN (PI):</div>
                        <div class="info-content">
                            <div style="display: flex; justify-content: space-between;"><span>No:</span><b
                                    id="order-no"></b></div>
                            <div style="display: flex; justify-content: space-between;"><span>PO:</span><span
                                    id="order-po"></span></div>
                            <div style="display: flex; justify-content: space-between;"><span>Tgl:</span><span
                                    id="order-date"></span></div>
                            <div style="display: flex; justify-content: space-between;"><span>Jatuh Tempo:</span><span
                                    id="order-due" style="color: #ef4444; font-weight: 600;"></span></div>
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-title">🚚 PENGIRIMAN (SJ):</div>
                        <div class="info-content">
                            <div style="display: flex; justify-content: space-between;"><span>No SJ:</span><b
                                    id="delivery-sj"></b></div>
                            <div style="display: flex; justify-content: space-between;"><span>Driver:</span><span
                                    id="delivery-driver"></span></div>
                            <div style="display: flex; justify-content: space-between;"><span>Plat:</span><span
                                    id="delivery-plate"></span></div>
                            <div style="display: flex; justify-content: space-between;"><span>Tgl Kirim:</span><span
                                    id="delivery-date"></span></div>
                        </div>
                    </div>
                </div>

                <table class="inv-table">
                    <thead>
                        <tr>
                            <th>Produk / Deskripsi</th>
                            <th style="text-align: center;">Qty</th>
                            <th style="text-align: center;">Satuan</th>
                            <th style="text-align: right;">Harga Satuan</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody id="item-list"></tbody>
                </table>

                <div style="display: grid; grid-template-columns: 1fr 350px; gap: 50px; margin-top: 30px;">
                    <div>
                        <div class="info-title">🖼️ BUKTI PEMBAYARAN:</div>
                        <div id="proof-display-area"
                            style="margin-top: 10px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; padding: 10px; text-align: center;">
                        </div>
                    </div>
                    <div>
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; color: #475569;">
                            <span>Subtotal</span><span id="fin-subtotal" style="font-weight: 600;"></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; color: #475569;">
                            <span>Pajak (PPN)</span><span id="fin-tax" style="font-weight: 600;"></span>
                        </div>
                        <div
                            style="display: flex; justify-content: space-between; padding: 15px 0 0; margin-top: 10px; border-top: 2px solid #0f172a;">
                            <span style="font-weight: 800; font-size: 14px;">TOTAL TAGIHAN</span>
                            <span id="fin-total" style="font-size: 1.6rem; font-weight: 900; color: #0f172a;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <iframe id="print-frame" style="display: none;"></iframe>

    <script>
        const formatIDR = (v) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(v);

        let hasProofInDB = false;

        function toggleInputs() {
            const method = document.getElementById('edit-method').value;
            const bankWrapper = document.getElementById('bank-wrapper');
            const proofLabel = document.getElementById('proof-status-label');

            if (method === 'TRANSFER') {
                bankWrapper.style.display = 'flex';
                proofLabel.innerText = hasProofInDB ? '(Boleh Update)' : '* WAJIB';
            } else {
                bankWrapper.style.display = 'none';
                proofLabel.innerText = '(Opsional)';
            }
        }

        function previewFile(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('image-preview-container').style.display = 'flex';
                };
                reader.readAsDataURL(file);
            }
        }

        async function fetchInvoiceDetail() {
            try {
                const response = await fetch(`/api/invoices/{{ $record->id }}`);
                const json = await response.json();
                const d = json.data;

                // Status Paid Logic
                const isPaid = d.status_pembayaran.toLowerCase() === 'paid';
                const badge = document.getElementById('badge-method');

                // "combined_status": 0, // 1 jika invoice ini di gabung
                const isCombined = d.combined_status === "1";
                const cancelBtn = document.getElementById('btn-cancel');
                if (isCombined) {
                    cancelBtn.classList.remove('hidden');
                } else {
                    cancelBtn.classList.add('hidden');
                }

                if (isPaid) {
                    document.getElementById('action-inputs').classList.add('hidden');
                    document.getElementById('btn-save').classList.add('hidden');
                    document.getElementById('action-title').innerText = "✅ Pembayaran Terkonfirmasi";
                    badge.innerText = `PAID VIA ${d.payment_info?.method || 'CASH'}`;
                    badge.style.background = "#dcfce7";
                    badge.style.color = "#166534";
                } else {
                    badge.innerText = "MENUNGGU PEMBAYARAN";
                    badge.style.background = "#fef3c7";
                    badge.style.color = "#92400e";
                }

                // Fill Info
                document.getElementById('inv-number').innerText = d.invoice_number;
                document.getElementById('cust-name').innerText = d.customer?.name || '-';
                document.getElementById('cust-address').innerText = d.customer?.address || '-';
                document.getElementById('order-no').innerText = d.data_pesanan?.number || '-';
                document.getElementById('order-po').innerText = d.data_pesanan?.po_number || '-';
                document.getElementById('order-date').innerText = d.data_pesanan?.date || '-';
                document.getElementById('order-due').innerText = d.data_pesanan?.due_date || '-';
                document.getElementById('delivery-sj').innerText = d.data_pengiriman?.no_sj || '-';
                document.getElementById('delivery-driver').innerText = d.data_pengiriman?.driver_name || '-';
                document.getElementById('delivery-plate').innerText = d.data_pengiriman?.vehicle_plate || '-';
                document.getElementById('delivery-date').innerText = d.data_pengiriman?.delivery_date || '-';

                // Financials
                document.getElementById('fin-subtotal').innerText = formatIDR(d.financials.subtotal);
                document.getElementById('fin-tax').innerText = formatIDR(d.financials.tax_amount);
                document.getElementById('fin-total').innerText = formatIDR(d.financials.total_amount);

                // Bank List
                const bankSelect = document.getElementById('edit-bank');
                bankSelect.innerHTML = '<option value="">-- Pilih Bank Tujuan --</option>';
                d.available_banks.forEach(bank => {
                    bankSelect.innerHTML +=
                        `<option value="${bank.id}">${bank.bank_name} - ${bank.account_number}</option>`;
                });

                // Proof Area
                const proofArea = document.getElementById('proof-display-area');
                if (d.payment_info?.proof_url) {
                    hasProofInDB = true;
                    proofArea.innerHTML =
                        `<img src="${d.payment_info.proof_url}" style="max-height: 250px; border-radius: 8px;">`;
                } else {
                    proofArea.innerHTML = `<p style="color: #94a3b8; font-style: italic;">Belum ada bukti.</p>`;
                }

                // Items Table
                document.getElementById('item-list').innerHTML = d.items.map(item => `
                    <tr>
                        <td>${item.product_name}</td>
                        <td style="text-align: center;">${item.qty}</td>
                        <td style="text-align: center;">${item.unit_name}</td>
                        <td style="text-align: right;">${formatIDR(item.unit_price)}</td>
                        <td style="text-align: right; font-weight: 800;">${formatIDR(item.total_item)}</td>
                    </tr>
                `).join('');

                // UI Finalize
                document.getElementById('loader').classList.add('hidden');
                document.getElementById('invoice-content').classList.remove('hidden');
                toggleInputs();

            } catch (err) {
                console.error(err);
                document.getElementById('loader').innerHTML = `<p style="color:red">Error Load Data</p>`;
            }
        }

        async function saveInvoiceData() {
            const method = document.getElementById('edit-method').value;
            const bankId = document.getElementById('edit-bank').value;
            const fileInput = document.getElementById('file-input');
            const notes = document.getElementById('edit-notes').value;

            if (method === 'TRANSFER' && !bankId) return alert('Pilih Bank!');

            const btn = document.getElementById('btn-save');
            btn.disabled = true;
            btn.innerText = "Processing...";

            const formData = new FormData();
            formData.append('payment_method', method);
            formData.append('bank_account_id', bankId);
            formData.append('notes', notes);
            if (fileInput.files[0]) formData.append('payment_proof', fileInput.files[0]);

            try {
                const res = await fetch(`/api/invoices/{{ $record->id }}/pay`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if (res.ok) {
                    alert('Berhasil Update!');
                    location.reload();
                } else {
                    alert('Gagal Update');
                    btn.disabled = false;
                }
            } catch (e) {
                alert('Server Error');
                btn.disabled = false;
            }
        }

        async function cancelInvoiceCombined() {
            // 1. MODAL KONFIRMASI MODERN
            const result = await Swal.fire({
                title: 'Konfirmasi Perubahan?',
                text: "Pastikan data revisi sudah sesuai sebelum disimpan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            // --- Loading State Mulai ---
            Swal.fire({
                title: 'Mohon Tunggu...',
                text: 'Sedang memproses data ke server',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const method = document.getElementById('edit-method').value;
            const bankId = document.getElementById('edit-bank').value;
            const fileInput = document.getElementById('file-input');
            const notes = document.getElementById('edit-notes').value;

            if (method === 'TRANSFER' && !bankId) {
                return Swal.fire('Error!', 'Silakan pilih Bank terlebih dahulu!', 'error');
            }

            const formData = new FormData();
            formData.append('payment_method', method);
            formData.append('bank_account_id', bankId);
            formData.append('notes', notes);
            if (fileInput.files[0]) formData.append('payment_proof', fileInput.files[0]);

            try {
                const res = await fetch(`/api/invoices/{{ $record->id }}/cancel-combined`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (res.ok) {
                    // 2. MODAL BERHASIL
                    await Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data Invoice telah diperbarui.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    location.reload();
                } else {
                    const error = await res.json();
                    Swal.fire('Gagal!', error.message || 'Terjadi kesalahan saat update.', 'error');
                }
            } catch (e) {
                Swal.fire('Server Error!', 'Tidak dapat terhubung ke server.', 'error');
            }
        }

        function printInvoice() {
            const url = "{{ route('invoice.print', $record->id) }}";
            const frame = document.getElementById('print-frame');

            document.title = "Invoice - {{ $record->invoice_number }}";

            frame.src = url;

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
        document.addEventListener('DOMContentLoaded', fetchInvoiceDetail);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-filament-panels::page>
