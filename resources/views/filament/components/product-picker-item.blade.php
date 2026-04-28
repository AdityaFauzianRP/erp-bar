<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@500&display=swap');

    .premium-picker-row {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 18px 24px;
        background: #ffffff;
        border-bottom: 1px solid rgba(226, 232, 240, 0.6);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: 'Plus Jakarta Sans', sans-serif;
        position: relative;
        overflow: hidden;
    }

    /* Efek Highlight Samping saat Hover */
    .premium-picker-row::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: #0061ff;
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .premium-picker-row:hover {
        background: #fcfdfe;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.03);
        z-index: 10;
    }

    .premium-picker-row:hover::before {
        transform: scaleY(1);
    }

    /* SKU Container */
    .col-sku {
        flex: 0 0 130px;
    }

    .sku-wrapper {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .sku-label {
        font-size: 9px;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 0.05em;
    }

    .sku-tag {
        background: #f8fafc;
        color: #475569;
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        width: fit-content;
    }

    /* Product Info */
    .col-info {
        flex: 1;
        padding-left: 30px;
    }

    .product-title {
        display: block;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        /* Slate 900 */
        letter-spacing: -0.02em;
        margin-bottom: 4px;
    }

    .meta-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .category-pill {
        font-size: 10px;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 4px;
    }

    /* Price Section - Made to look like a Bank Statement */
    .col-price {
        flex: 0 0 200px;
        text-align: right;
        padding-right: 40px;
    }

    .price-context {
        font-size: 10px;
        font-weight: 700;
        color: #6366f1;
        /* Indigo */
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 2px;
        display: block;
    }

    .price-amount {
        font-size: 18px;
        font-weight: 800;
        color: #1e293b;
        display: flex;
        align-items: baseline;
        justify-content: flex-end;
        gap: 4px;
    }

    .currency-symbol {
        font-size: 12px;
        font-weight: 600;
        color: #94a3b8;
    }

    /* Elegant Status Badge */
    .col-status {
        flex: 0 0 120px;
        text-align: center;
    }

    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: #ecfdf5;
        /* Emerald 50 */
        color: #059669;
        /* Emerald 600 */
        font-size: 11px;
        font-weight: 700;
        border-radius: 10px;
        border: 1px solid #d1fae5;
    }

    .dot {
        height: 6px;
        width: 6px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }
</style>

<div class="premium-picker-row">
    <div class="col-sku">
        <div class="sku-wrapper">
            <span class="sku-label">KODE BARANG</span>
            <span class="sku-tag">{{ $code }}</span>
        </div>
    </div>

    <div class="col-info">
        <span class="product-title">{{ $name }}</span>
        <div class="meta-container">
            <span class="category-pill">NAMA BARANG</span>
            <span style="font-size: 10px; color: #cbd5e1;">•</span>
            <span style="font-size: 11px; color: #94a3b8;">Ref No: #PRO-{{ rand(1000, 9999) }}</span>
        </div>
    </div>

    <div class="col-price">
        <span class="price-context">Harga Satuan</span>
        <div class="price-amount">
            <span class="currency-symbol">Rp</span>
            <span>{{ $price }}</span>
        </div>
    </div>

    <div class="col-status">
        <div class="status-indicator">
            <span class="dot"></span>
            ACTIVE
        </div>
    </div>
</div>
