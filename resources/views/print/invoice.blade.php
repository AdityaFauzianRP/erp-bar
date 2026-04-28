<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice - PT MITRA RAYA VAMILY</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #334155;
            background: #f1f5f9;
            line-height: 1.2;
            /* Lebih rapat */
            padding: 20px;
        }

        .invoice-container {
            max-width: 750px;
            margin: auto;
            background: #fff;
            padding: 30px;
            position: relative;
        }

        /* Watermark Status */
        .status-watermark {
            position: absolute;
            top: 120px;
            right: 30px;
            transform: rotate(-15deg);
            font-size: 40pt;
            font-weight: 900;
            opacity: 0.04;
            z-index: 1;
            text-transform: uppercase;
            pointer-events: none;
        }

        /* TOP HEADER: Perusahaan vs Judul Dokumen */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 15px;
        }

        .company-info h1 {
            color: #1e3a8a;
            font-size: 14pt;
            /* Ukuran pas untuk nama PT */
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .company-info p {
            font-size: 8pt;
            color: #475569;
            line-height: 1.4;
        }

        .document-title {
            text-align: right;
        }

        .document-title h2 {
            color: #1e3a8a;
            font-size: 20pt;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .invoice-label {
            background: #1e3a8a;
            color: white !important;
            padding: 4px 10px;
            border-radius: 3px;
            font-weight: 700;
            font-size: 9pt;
            display: inline-block;
            margin-top: 5px;
            -webkit-print-color-adjust: exact;
        }

        /* Grid Info */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 7pt;
            font-weight: 700;
            color: #1e3a8a;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
            margin-bottom: 6px;
        }

        .info-content strong {
            font-size: 9pt;
            color: #0f172a;
        }

        .info-content p {
            font-size: 8pt;
            color: #475569;
        }

        /* TABLE COMPACT */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        thead th {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 7.5pt;
            padding: 8px 10px;
            text-align: left;
            -webkit-print-color-adjust: exact;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 8pt;
        }

        .product-name {
            font-weight: 600;
            color: #1e293b;
        }

        /* Summary Area */
        .summary-container {
            display: grid;
            grid-template-columns: 1.3fr 0.7fr;
            gap: 20px;
        }

        .payment-box {
            background: #f8fafc;
            padding: 10px;
            border-radius: 4px;
            border-left: 3px solid #1e3a8a;
            font-size: 8pt;
        }

        .total-box {
            font-size: 8.5pt;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .total-row.grand-total {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1.5px solid #1e3a8a;
            font-size: 11pt;
            font-weight: 800;
            color: #1e3a8a;
        }

        /* Signature */
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }

        .sig-box {
            width: 150px;
            font-size: 8pt;
        }

        .sig-line {
            margin-top: 100px;
            border-bottom: 1px solid #334155;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .invoice-container {
                max-width: 100%;
                box-shadow: none;
                padding: 10px;
            }

            #loading {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div id="loading" style="text-align:center; padding:50px; font-size: 9pt;">Memuat Data...</div>

    <div id="print-area" class="invoice-container" style="display: none;">
        <div id="status_watermark" class="status-watermark"></div>

        <div class="top-header">
            <div class="company-info">
                <h1>PT MITRA RAYA VAMILY</h1>
                <p>The Nanjung Regency Blok C-80, Cimahi, Jawa Barat</p>
            </div>
            <div class="document-title">
                <h2>INVOICE</h2>
                <div class="invoice-label" id="inv_number"></div>
                <p style="font-size: 8pt; margin-top: 4px;">Tanggal: <strong id="inv_date"></strong></p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-content">
                <div class="section-title">Ditujukan Kepada</div>
                <strong id="cust_name"></strong>
                <p id="cust_address" style="margin-top:2px;"></p>
            </div>
            <div class="info-content">
                <div class="section-title">Detail Transaksi</div>
                <p>No. Proforma: <strong id="ref_pi"></strong></p>
                <p>No. Surat Jalan: <strong id="ref_sj"></strong></p>
                <p>No. PO Customer: <strong id="ref_po_customer"></strong></p>
                <p>Jatuh Tempo: <strong id="ref_due" style="color: #be123c;"></strong></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Produk</th>
                    <th class="text-center" style="width: 70px;">Qty</th>
                    <th class="text-center" style="width: 70px;">Satuan</th>
                    <th class="text-right" style="width: 100px;">Harga</th>
                    <th class="text-right" style="width: 100px;">Total</th>
                </tr>
            </thead>
            <tbody id="item_list"></tbody>
        </table>

        <div class="summary-container">
            <div>
                <div class="section-title">Informasi Pembayaran</div>
                <div class="payment-box" id="pay_info"></div>
                {{-- note di buat bagus --}}

                <div id="inv_notes" style="margin-top: 6px; font-size: 7.5pt; color: #64748b; font-style: italic;">
                </div>
            </div>
            <div class="total-box">
                <div class="section-title">Total Pembayaran</div>
                <div class="total-row">
                    <span>Subtotal</span>
                    <span id="sum_sub"></span>
                </div>
                <div class="total-row">
                    <span>PPN</span>
                    <span id="sum_tax"></span>
                </div>
                <div class="total-row grand-total">
                    <span>TOTAL</span>
                    <span id="sum_grand"></span>
                </div>
            </div>
        </div>

        <div class="signature-section">
            <div class="sig-box">
                <p>Diterima Oleh,</p>
                <div class="sig-line"></div>
            </div>
            <div class="sig-box">
                <p>Hormat Kami,</p>
                <div class="sig-line"></div>
                <p style="font-size: 7pt; margin-top: 3px; font-weight: bold;">Finance Dept</p>
            </div>
        </div>
    </div>

    <script>
        const formatCurrency = (val) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(val || 0).replace("Rp", "");

        const terbilang = (n) => {
            if (n < 0) return "minus " + terbilang(Math.abs(n));
            const unit = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh",
                "sebelas"
            ];
            let hasil = "";
            if (n < 12) hasil = unit[n];
            else if (n < 20) hasil = terbilang(n - 10) + " belas";
            else if (n < 100) hasil = terbilang(Math.floor(n / 10)) + " puluh " + terbilang(n % 10);
            else if (n < 200) hasil = "seratus " + terbilang(n - 100);
            else if (n < 1000) hasil = terbilang(Math.floor(n / 100)) + " ratus " + terbilang(n % 100);
            else if (n < 2000) hasil = "seribu " + terbilang(n - 1000);
            else if (n < 1000000) hasil = terbilang(Math.floor(n / 1000)) + " ribu " + terbilang(n % 1000);
            else if (n < 1000000000) hasil = terbilang(Math.floor(n / 1000000)) + " juta " + terbilang(n % 1000000);
            else if (n < 1000000000000) hasil = terbilang(Math.floor(n / 1000000000)) + " milyar " + terbilang(n %
                1000000000);
            return hasil.replace(/\s+/g, ' ').trim();
        };

        async function startApp() {
            try {
                const segments = window.location.pathname.split('/').filter(s => s !== "");
                const invId = segments[segments.indexOf('invoices') + 1];
                const response = await fetch(`/api/invoices/${invId}`);
                const json = await response.json();
                const d = json.data;
                
                document.getElementById('inv_number').innerText = "#" + d.invoice_number;
                document.getElementById('inv_date').innerText = d.data_pesanan.date;
                document.getElementById('status_watermark').innerText = d.status_pembayaran;
                document.getElementById('cust_name').innerText = d.customer_brand.brand_name;
                document.getElementById('cust_address').innerText = d.customer_brand.nama_cabang;
                document.getElementById('ref_pi').innerText = d.data_pesanan.number;
                document.getElementById('ref_sj').innerText = d.data_pengiriman.no_sj;
                document.getElementById('ref_due').innerText = d.data_pesanan.due_date;
                document.getElementById('ref_po_customer').innerText = d.data_pesanan.po_number;

                const list = document.getElementById('item_list');
                d.items.forEach(item => {

                    const qtyVal = parseFloat(item.qty_received_good || item.qty || 0);
                    nomor = parseInt(list.children.length) + 1;

                    if (qtyVal > 0) {

                        list.innerHTML += `
                                <tr>
                                    <td style="text-align:left">${nomor}</td>
                                    <td><div class="product-name">${item.product_name}</div></td>
                                    <td style="text-align:left">${parseFloat(item.qty)}</td>
                                    <td style="text-align:left">${item.unit_name}</td>
                                    <td style="text-align:left">${formatCurrency(item.unit_price)}</td>
                                    <td style="text-align:left; font-weight:700; color:#1e3a8a">${formatCurrency(item.total_item)}</td>
                                </tr>`;
                    }
                })

                let payHtml = `<strong>${d.payment_info.method}</strong><br>`;
                if (d.payment_info.method === 'TRANSFER') {
                    const bank = d.available_banks.find(b => b.id == d.payment_info.bank_account_id);
                    if (bank) payHtml +=
                        `${bank.bank_name} - ${bank.account_number}<br>A/N: ${bank.account_holder} <br> <br> Harap melakukan pembayaran sebelum tanggal jatuh tempo. Konfirmasi pembayaran ke nomor WhatsApp kami.`;
                }
                document.getElementById('pay_info').innerHTML = payHtml;
                document.getElementById('inv_notes').innerText = d.notes ? `Catatan: ${d.notes}` : "";

                document.getElementById('sum_sub').innerText = "Rp " + formatCurrency(d.financials.subtotal);
                document.getElementById('sum_tax').innerText = "Rp " + formatCurrency(d.financials.tax_amount);
                document.getElementById('sum_grand').innerText = "Rp " + formatCurrency(d.financials.total_amount);

                // tERBILAN
                const grand = d.financials.total_amount;
                const terbilangText = terbilang(grand);

                // Buat elemen
                const terbilangEl = document.createElement('div');
                terbilangEl.style.fontSize = '8pt'; // Sedikit diperbesar agar terbaca
                terbilangEl.style.color = '#1e3a8a'; // Gunakan warna biru yang sama dengan tema PT
                terbilangEl.style.fontStyle = 'italic';
                terbilangEl.style.marginTop = '8px'; // Beri jarak dari angka total
                terbilangEl.style.textAlign = 'right'; // Rata kanan
                terbilangEl.style.textTransform = 'capitalize'; // Huruf kapital di awal kata
                terbilangEl.style.borderTop = '1px dashed #cbd5e1'; // Beri garis putus-putus tipis
                terbilangEl.style.paddingTop = '5px';

                terbilangEl.innerText = `# ${terbilangText} rupiah #`;

                // KUNCI: Gunakan .after() pada parent element (baris grand-total)
                document.querySelector('.total-row.grand-total').after(terbilangEl);



                document.getElementById('loading').style.display = 'none';
                document.getElementById('print-area').style.display = 'block';

                

                // setTimeout(() => {
                //     window.print();
                // }, 800);
            } catch (err) {
                document.getElementById('loading').innerText = "Gagal memuat: " + err.message;
            }
        }
        startApp();
    </script>
</body>

</html>
