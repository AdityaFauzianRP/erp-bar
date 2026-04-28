<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Sales Order - {{ $record->number }}</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background: #f1f5f9;
            font-size: 8.5pt;
            /* Ukuran font dasar diperkecil */
        }

        .invoice-card {
            width: 210mm;
            min-height: 297mm;
            background: white;
            margin: auto;
            padding: 1.2cm;
            /* Padding diperkecil */
            box-sizing: border-box;
            position: relative;
        }

        .top-accent {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: #1e3a8a;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
            /* Spasi dikurangi */
        }

        .brand h1 {
            margin: 0;
            font-size: 16pt;
            /* Judul diperkecil */
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
        }

        .brand p {
            margin: 2px 0 0;
            font-size: 7.5pt;
            color: #64748b;
            line-height: 1.3;
        }

        .doc-type {
            text-align: right;
        }

        .doc-type h2 {
            margin: 0;
            font-size: 18pt;
            /* Ukuran dokumen diperkecil */
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .ref-box {
            margin-top: 5px;
            font-family: monospace;
            font-size: 10pt;
            color: #1e3a8a;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 4px;
            display: inline-block;
            font-weight: 700;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }

        .info-block {
            background: #f8fafc;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .info-label {
            font-size: 7pt;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            display: block;
        }

        .info-value {
            font-size: 10pt;
            font-weight: 700;
            color: #1e293b;
        }

        /* Table Styles Compact */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead tr th {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            padding: 8px 10px !important;
            font-size: 7.5pt !important;
            text-transform: uppercase !important;
            font-weight: 700 !important;
            text-align: left;
            -webkit-print-color-adjust: exact;
        }

        tbody td {
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 8px 10px !important;
            font-size: 8pt !important;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: 700;
        }

        .color-blue {
            color: #1e3a8a;
        }

        .summary-container {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }

        .notes-area {
            flex: 1;
            border: 1px dashed #cbd5e1;
            padding: 10px;
            border-radius: 8px;
            font-size: 7.5pt;
            min-height: 60px;
        }

        .total-area {
            width: 280px;
            /* Ukuran total area diperkecil */
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 8.5pt;
            border-bottom: 1px solid #f1f5f9;
        }

        .grand-total {
            margin-top: 8px;
            padding: 10px;
            background: #1e3a8a;
            border-radius: 6px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            -webkit-print-color-adjust: exact;
        }

        .grand-total-amount {
            font-size: 14pt;
            font-weight: 800;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 100px;
            margin-top: 40px;
            text-align: center;
        }

        .sig-box {
            border-top: 1px solid #0f172a;
            padding-top: 5px;
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
        }

        @media print {
            body {
                background: white;
            }

            .invoice-card {
                box-shadow: none;
                padding: 1cm;
                width: 100%;
            }

            #loading {
                display: none !important;
            }

            .top-accent,
            .grand-total {
                -webkit-print-color-adjust: exact;
            }
        }

        /* Spinner diperkecil */
        .spinner {
            width: 30px;
            height: 30px;
            border-width: 3px;
        }
    </style>
</head>

<body>

    <div id="loading"
        style="position:fixed; inset:0; background:white; display:flex; flex-direction:column; align-items:center; justify-content:center; z-index:100;">
        <div class="spinner"
            style="border:3px solid #f3f3f3; border-top:3px solid #1e3a8a; border-radius:50%; width:30px; height:30px; animation:spin 1s linear infinite;">
        </div>
        <p style="font-size: 10pt; color: #1e3a8a; margin-top: 10px;">Memuat Data...</p>
    </div>

    <div class="invoice-card" id="print-area" style="visibility: hidden;">
        <div class="top-accent"></div>

        <div class="header">
            <div class="brand">
                <h1>PT MITRA RAYA VAMILY</h1>
                <p>Jl. Rajawali Timur No.108 42c, Ciroyom, Kec. Andir, Kota Bandung, Jawa Barat 40182</p>
            </div>
            <div class="doc-type">
                <h2>SALES ORDER</h2>
                <div class="ref-box" id="order-number">...</div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-block">
                <span class="info-label">Customer / Penagihan</span>
                <div class="info-value color-blue" id="cust-name">...</div>
                <div style="font-size: 8pt; font-weight: 600; margin: 2px 0;" id="cust-branch">...</div>
                <div style="font-size: 7.5pt; color: #64748b; line-height: 1.2;" id="cust-address">...</div>
            </div>
            <div class="info-block" style="text-align: right;">
                <span class="info-label">Detail & Referensi</span>
                <table style="width: 100%; font-size: 8pt; margin: 0;">
                    <tr>
                        <td class="text-right" style="padding: 2px 0;">Tgl Kirim:</td>
                        <td class="text-right font-bold color-blue" id="date-delivery" style="padding: 2px 0;">-</td>
                    </tr>
                    <tr>
                        <td class="text-right" style="padding: 2px 0;">Jatuh Tempo:</td>
                        <td class="text-right font-bold" id="date-due" style="padding: 2px 0;">-</td>
                    </tr>
                    <tr>
                        <td class="text-right" style="padding: 2px 0;">No. PO:</td>
                        <td class="text-right font-bold" id="po-ref" style="padding: 2px 0;">-</td>
                    </tr>
                </table>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 30px;" class="text-center">#</th>
                    <th>Produk</th>
                    <th class="text-center" style="width: 50px;">Qty</th>
                    <th class="text-center" style="width: 50px;">Unit</th>
                    <th class="text-right" style="width: 90px;">Harga</th>
                    <th class="text-right" style="width: 100px;">Total</th>
                </tr>
            </thead>
            <tbody id="item-list">
            </tbody>
        </table>

        <div class="summary-container">
            <div class="notes-area">
                <span class="info-label">Catatan</span>
                <div id="order-notes">...</div>
            </div>
            <div class="total-area">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span class="font-bold" id="subtotal">0</span>
                </div>
                <div class="total-row">
                    <span>PPN (<span id="tax-percent">0</span>%)</span>
                    <span class="font-bold" id="tax-amount">0</span>
                </div>
                <div class="grand-total">
                    <span style="font-size: 8pt; font-weight: 700;">TOTAL</span>
                    <span class="grand-total-amount" id="grand-total">0</span>
                </div>
            </div>
        </div>

        <div class="signature-grid">
            <div>
                <div style="height: 50px;"></div>
                <div class="sig-box">Customer / Penerima</div>
            </div>
            <div>
                <div style="height: 50px;"></div>
                <div class="sig-box">Authorized Signature</div>
            </div>
        </div>
    </div>

    <script>
        const formatCurrency = (val) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(val || 0).replace("Rp", "Rp ");
        }

        const formatDate = (dateString) => {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        async function init() {
            const recordId = "{{ $record->id }}";
            try {
                const response = await axios.get(`/api/proforma-invoices/${recordId}`);
                const d = response.data.data;

                document.getElementById('order-number').innerText = d.number || '-';
                document.getElementById('cust-name').innerText = d.customer_induk?.name || '-';
                document.getElementById('cust-branch').innerText =
                    `${d.customer_brand?.brand_name || ''}`;
                document.getElementById('cust-address').innerText = d.customer_brand?.nama_cabang
                    ?.head_office_address || '-';
                document.getElementById('date-delivery').innerText = formatDate(d.delivery_deadline);
                document.getElementById('date-due').innerText = formatDate(d.due_date);
                document.getElementById('po-ref').innerText = d.po_number || '-';
                document.getElementById('order-notes').innerText = d.notes || '-';

                const tbody = document.getElementById('item-list');
                tbody.innerHTML = '';
                d.items.forEach((item, index) => {
                    tbody.innerHTML += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td><span class="font-bold">${item.product_name}</span></td>
                            <td class="text-center font-bold">${parseFloat(item.qty)}</td>
                            <td class="text-center uppercase">${item.unit ? item.unit.short_name : 'PCS'}</td>
                            <td class="text-right">${formatCurrency(item.unit_price)}</td>
                            <td class="text-right font-bold color-blue">${formatCurrency(item.subtotal)}</td>
                        </tr>`;
                });

                const sub = parseFloat(d.total_amount || 0);
                const tax = parseFloat(d.ppn_amount || 0);
                document.getElementById('subtotal').innerText = formatCurrency(sub);
                document.getElementById('tax-percent').innerText = d.ppn_percent || 0;
                document.getElementById('tax-amount').innerText = formatCurrency(tax);
                document.getElementById('grand-total').innerText = formatCurrency(sub + tax);

                document.getElementById('loading').style.display = 'none';
                document.getElementById('print-area').style.visibility = 'visible';
            } catch (err) {
                document.getElementById('loading').innerHTML =
                    `<p style="color:red">Gagal memuat data: ${err.message}</p>`;
            }
        }
        window.onload = init;
    </script>
    <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</body>

</html>
