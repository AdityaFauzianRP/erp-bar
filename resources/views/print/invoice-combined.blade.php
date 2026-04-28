<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tukar Faktur Gabungan — PT MITRA RAYA VAMILY</title>
    <style>
        /* CSS RESET & PRINT RULES */
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            background: #f1f5f9;
            color: #334155;
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* PAGE SETTINGS */
        .page {
            width: 210mm;
            min-height: 290mm; /* Gunakan min-height, bukan height tetap */
            margin: 10px auto;
            background: #ffffff;
            padding: 10mm 15mm;
            position: relative;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            /* HAPUS overflow: hidden; agar konten yang panjang tidak terpotong */
        }

        @media print {
            @page {
                size: A4;
                margin: 5mm; /* Beri sedikit margin agar print lebih rapi */
            }
        
            body {
                background: none;
                margin: 0;
            }
        
            .page {
                margin: 0;
                box-shadow: none;
                width: 100%;
                height: auto; /* Biarkan tinggi menyesuaikan konten */
                min-height: initial;
                page-break-after: always; /* Setiap faktur beda kertas */
            }
        
            /* Agar baris tabel tidak terpotong di tengah-tengah baris */
            table { page-break-inside: auto; }
            tr    { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; } /* Re-print header jika tabel pindah halaman */
        }

        /* HEADER STYLE */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }

        .company-info h1 {
            font-size: 14pt;
            color: #1e40af;
            font-weight: 800;
            margin: 0;
        }

        .company-info p {
            font-size: 8pt;
            color: #64748b;
            margin: 2px 0;
        }

        .doc-title {
            text-align: right;
        }

        .doc-title h2 {
            font-size: 20pt;
            color: #1e40af;
            margin: 0;
        }

        .inv-number {
            background: #1e40af;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 700;
            display: inline-block;
            margin-top: 5px;
        }

        /* GRID & TABLES */
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 8pt;
            font-weight: 800;
            color: #1e40af;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background: #f8fafc;
            color: #64748b;
            font-size: 7.5pt;
            text-align: left;
            padding: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 8.5pt;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: 700;
            color: #0f172a;
        }

        /* TOTAL CARD */
        .total-card {
            background: #1e40af;
            color: white;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-amount {
            font-size: 18pt;
            font-weight: 800;
        }

        /* SIGNATURE */
        .sign-area {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 40px;
            text-align: center;
        }

        .sign-box {
            border-bottom: 1px solid #334155;
            height: 60px;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>

    <div id="main-container">
        <div style="text-align:center; padding:50px;">Memproses Dokumen...</div>
    </div>

    <script>
        const RECORD_ID = "{{ $record->id }}";
        const API_URL = `https://mrvfresh.com/api/invoices/showCombined/${RECORD_ID}`;

        const fmtCurrency = (n) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(n || 0);

        function terbilang(n) {
            if (n < 0) return "minus " + terbilang(Math.abs(n));
            // if (n === 0) return "nol";

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

        async function init() {
            try {
                const r = await fetch(API_URL);
                const json = await r.json();
                const data = json.data;
                const container = document.getElementById('main-container');
                container.innerHTML = '';

                // --- 1. GENERATE LEMBAR INDIVIDUAL INVOICE ---
                data.invoices.forEach((inv) => {
                    const page = document.createElement('div');
                    page.className = 'page';

                    // Filter item berdasarkan delivery_id YANG COCOK 
                    // DAN yang memiliki qty_received_good lebih besar dari 0
                    const invoiceItems = data.items.filter(item => {
                        return item.delivery_id === inv.delivery_id && item.qty_received_good > 0;
                    });

                    // Jika setelah difilter ternyata invoice ini tidak punya item sama sekali, 
                    // Anda bisa memilih untuk tidak menampilkan halaman ini
                    if (invoiceItems.length === 0) return;

                    page.innerHTML = `
                        <div class="top-header">
                            <div class="company-info">
                                <h1>PT MITRA RAYA VAMILY</h1>
                                <p>Jl. Rajawali Timur No.108 42c, Ciroyom, Kec. Andir, Kota Bandung, Jawa Barat 40182</p>
                            </div>
                            <div class="doc-title">
                                <h2>TUKAR FAKTUR</h2>
                                <div class="inv-number">#${inv.invoice_number}</div>
                            </div>
                        </div>

                        <div class="grid">
                            <div>
                                <div class="section-title">Tagihan Untuk</div>
                                <p class="bold">${data.customer.name}</p>
                                <p>${data.customer.address}</p>
                                <p style="margin-top:5px">Cabang: <b>${inv.brand_name} (${inv.kota_cabang})</b></p>
                            </div>
                            <div>
                                <div class="section-title">Informasi Dokumen</div>
                                <p>Tgl Invoice: <b>${inv.invoice_date}</b></p>
                                <p>No. SJ: <b>${inv.no_sj}</b></p>
                                <p>No. Proforma: <b>${inv.pi_number}</b></p>
                                <p>No. PO Customer: <b>${inv.po_number}</b></p>
                            </div>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Deskripsi Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Harga</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${invoiceItems.map(it => `
                                    <tr>
                                        <td class="bold">
                                            ${it.product_name}
                                            ${it.qty_wasted > 0 ? `<br><small style="color:red">Terbuang: ${it.qty_wasted} ${it.unit_name}</small>` : ''}
                                        </td>
                                        <td class="text-center">${it.qty_received_good} ${it.unit_name}</td>
                                        <td class="text-right">${fmtCurrency(it.unit_price)}</td>
                                        <td class="text-right bold">${fmtCurrency(it.total_item)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>

                        <div style="display: flex; justify-content: flex-end;">
                            <div style="width: 250px;">
                                <div style="display:flex; justify-content:space-between; padding: 5px 0;">
                                    <span>Subtotal</span><span>${fmtCurrency(inv.subtotal)}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; padding: 5px 0; border-top: 1px solid #eee;">
    <span class="bold">Total Faktur</span><span class="bold">${fmtCurrency(inv.total_amount)}</span>
</div>
<div style="text-align: right; font-style: italic; font-size: 8pt; color: #1e40af; text-transform: capitalize; margin-top: 2px;">
    # ${terbilang(inv.total_amount)} rupiah #
</div>
                            </div>
                        </div>

                        <div style="margin-top: 20px; font-size: 8pt; color: #64748b;">
                            <p>Catatan: Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.</p>
                        </div>
                    `;
                    container.appendChild(page);
                });

                // --- 2. GENERATE LEMBAR AKHIR (SUMMARY) ---
                const summaryPage = document.createElement('div');
                summaryPage.className = 'page';

                // Cari bank yang dipilih berdasarkan bank_account_id di payment_info
                const selectedBank = data.available_banks.find(b => b.id === data.payment_info?.bank_account_id) ||
                    data.available_banks[0];

                summaryPage.innerHTML = `
                    <div class="top-header">
                        <div class="company-info">
                            <h1>PT MITRA RAYA VAMILY</h1>
                            <p>Ringkasan Tagihan Gabungan (Combined Invoice)</p>
                        </div>
                        <div class="doc-title">
                            <h2 style="font-size: 16pt;">TUKAR FAKTUR</h2>
                            <div class="inv-number">${data.combined_number}</div>
                        </div>
                    </div>

                    <div class="section-title">Daftar Faktur Terlampir</div>
                    <table>
                        <thead>
                            <tr>
                                <th>No. Faktur</th>
                                <th>No. SJ</th>
                                <th>Unit / Cabang</th>
                                <th class="text-right">Total Tagihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.invoices.map(inv => `
                                                                                                <tr>
                                                                                                    <td class="bold">${inv.invoice_number}</td>
                                                                                                    <td>${inv.no_sj}</td>
                                                                                                    <td>${inv.brand_name}/${inv.kota_cabang}</td>
                                                                                                    <td class="text-right bold">${fmtCurrency(inv.total_amount)}</td>
                                                                                                </tr>
                                                                                            `).join('')}
                        </tbody>
                    </table>

                    <div class="grid" style="margin-top:30px">
                        <div style="background:#f8fafc; padding:15px; border-radius:8px; border: 1px solid #e2e8f0;">
                            <div class="section-title">Informasi Pembayaran</div>
                            <p>Bank: <b>${selectedBank.bank_name}</b></p>
                            <p>No. Rekening: <b>${selectedBank.account_number}</b></p>
                            <p>Atas Nama: <b>${selectedBank.account_holder}</b></p>
                        </div>
                        <div>
                            <div class="total-card">
    <span>TOTAL TAGIHAN</span>
    <div style="text-align: right;">
        <div class="total-amount">${fmtCurrency(data.financials.total_amount)}</div>
        <div style="font-size: 8pt; font-weight: normal; font-style: italic; text-transform: capitalize; color: #e2e8f0; margin-top: 4px;">
            # ${terbilang(data.financials.total_amount)} rupiah #
        </div>
    </div>
</div>
                            <p style="font-size: 8pt; margin-top: 10px; color: #64748b;">
                                Mohon lakukan pembayaran sebelum tanggal <b>${data.due_date}</b>.
                            </p>
                        </div>
                    </div>

                    <div class="sign-area">
                        <div>
                            <p>Diterima Oleh,</p>
                            <div class="sign-box"></div>
                            <p>( ${data.customer.name} )</p>
                        </div>
                        <div>
                            <p>Hormat Kami,</p>
                            <div class="sign-box"></div>
                            <p><b>PT MITRA RAYA VAMILY</b></p>
                        </div>
                    </div>
                `;
                container.appendChild(summaryPage);

                while (container.nextSibling) {
                    container.parentNode.removeChild(container.nextSibling);
                }

                // Auto Print
                // setTimeout(() => {
                //     window.print();
                // }, 1000);

            } catch (err) {
                document.getElementById('main-container').innerHTML =
                    `<div style="color:red; padding:20px;">Error: ${err.message}</div>`;
            }
        }

        init();
    </script>
</body>

</html>
