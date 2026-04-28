@props(['dari', 'sampai'])

@php
    // Memanggil mapping terpusat dari Model
    $data = \App\Models\ExecSummaryFact::getSummary($dari, $sampai);

    if ($data) {
        $totalSales = $data->sales_paid + $data->sales_unpaid;
        $totalPurchase = $data->purchase_paid + $data->purchase_unpaid;
        $totalWaste = $data->waste_sales + $data->waste_warehouse + $data->waste_purchase;

        $labaKotor = $totalSales - $totalPurchase - $totalWaste;
        $labaBersih = $labaKotor - $data->opex;

        $fmt = fn($v) => 'Rp ' . number_format($v, 0, ',', '.');
    }
@endphp

<style>
    .report-container {
        font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        margin-bottom: 2rem;
    }

    /* Styling Filter */
    .filter-wrapper {
        background: #ffffff;
        padding: 1.25rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: flex-end;
        gap: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .dark .filter-wrapper {
        background: #1f2937;
        border-color: #374151;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        flex: 1;
    }

    .filter-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #4b5563;
        text-transform: uppercase;
    }

    .dark .filter-label {
        color: #9ca3af;
    }

    .filter-input {
        width: 100%;
        padding: 0.6rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background: #f9fafb;
        font-size: 0.875rem;
        color: #1f2937;
    }

    .dark .filter-input {
        background: #111827;
        border-color: #4b5563;
        color: white;
    }

    /* Card & Table */
    .report-card {
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .dark .report-card {
        background: #1f2937;
        border-color: #374151;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table thead th {
        background-color: #1e40af;
        color: #ffffff;
        padding: 10px 15px;
        text-align: left;
        font-size: 0.85rem;
        text-transform: uppercase;
        border: 1px solid #1e40af;
    }

    .report-table td {
        padding: 8px 15px;
        border: 1px solid #d1d5db;
        font-size: 0.9rem;
        color: #1f2937;
    }

    .dark .report-table td {
        border-color: #4b5563;
        color: #e5e7eb;
    }

    .text-right {
        text-align: right;
    }

    .font-bold {
        font-weight: 700;
    }

    .row-total {
        background-color: #ffffff;
        font-weight: 800;
    }

    .row-spacer {
        height: 15px;
        background-color: #f9fafb;
    }

    .dark .row-spacer {
        background-color: #111827;
    }

    .btn-excel {
        background-color: #16a34a;
        color: white;
        padding: 0.6rem 1.25rem;
        border-radius: 0.375rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        height: 42px;
    }

    .btn-excel:hover {
        background-color: #15803d;
    }
</style>

<div class="report-container">

    <div class="filter-wrapper">
        <div class="filter-group">
            <span class="filter-label">Dari Tanggal</span>
            <input type="date" wire:model.live="tableFilters.Periode.dari" class="filter-input">
        </div>

        <div class="filter-group">
            <span class="filter-label">Sampai Tanggal</span>
            <input type="date" wire:model.live="tableFilters.Periode.sampai" class="filter-input">
        </div>


    </div>

    <div class="report-card">
        @if ($data)
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 65%;">KETERANGAN</th>
                        <th class="text-right">NILAI</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- SECTION PENJUALAN --}}
                    <tr>
                        <td>Penjualan Sudah Dibayar</td>
                        <td class="text-right">{{ $fmt($data->sales_paid) }}</td>
                    </tr>
                    <tr>
                        <td>Penjualan Belum Dibayar</td>
                        <td class="text-right">{{ $fmt($data->sales_unpaid) }}</td>
                    </tr>
                    <tr class="row-total">
                        <td>Total Penjualan</td>
                        <td class="text-right">{{ $fmt($totalSales) }}</td>
                    </tr>

                    <tr class="row-spacer">
                        <td colspan="2"></td>
                    </tr>

                    {{-- SECTION PEMBELIAN --}}
                    <tr>
                        <td>Pembelian Sudah Dibayar</td>
                        <td class="text-right">{{ $fmt($data->purchase_paid) }}</td>
                    </tr>
                    <tr>
                        <td>Pembelian Belum Dibayar</td>
                        <td class="text-right">{{ $fmt($data->purchase_unpaid) }}</td>
                    </tr>
                    <tr class="row-total">
                        <td>Total Pembelian</td>
                        <td class="text-right">{{ $fmt($totalPurchase) }}</td>
                    </tr>

                    <tr class="row-spacer">
                        <td colspan="2"></td>
                    </tr>

                    {{-- SECTION BARANG RUSAK --}}
                    <tr>
                        <td>Barang Rusak/Busuk Penjualan</td>
                        <td class="text-right">{{ $fmt($data->waste_sales) }}</td>
                    </tr>
                    <tr>
                        <td>Barang Rusak/Busuk Gudang</td>
                        <td class="text-right">{{ $fmt($data->waste_warehouse) }}</td>
                    </tr>
                    <tr>
                        <td>Barang Rusak/Busuk Pembelian</td>
                        <td class="text-right">{{ $fmt($data->waste_purchase) }}</td>
                    </tr>
                    <tr class="row-total">
                        <td>Total Barang Rusak/Busuk</td>
                        <td class="text-right">{{ $fmt($totalWaste) }}</td>
                    </tr>

                    <tr class="row-spacer">
                        <td colspan="2"></td>
                    </tr>

                    {{-- SECTION LABA --}}
                    <tr class="font-bold">
                        <td>Laba Kotor</td>
                        <td class="text-right {{ $labaKotor < 0 ? 'text-red-600' : '' }}">
                            {{ $labaKotor < 0 ? '-' : '' }}{{ $fmt(abs($labaKotor)) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Pengeluaran Operasional</td>
                        <td class="text-right">{{ $fmt($data->opex) }}</td>
                    </tr>
                    <tr class="font-bold" style="background-color: #eff6ff;">
                        <td style="color: #1e40af;">Laba Bersih</td>
                        <td class="text-right" style="color: #1e40af;">
                            {{ $labaBersih < 0 ? '-' : '' }}{{ $fmt(abs($labaBersih)) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @else
            <div style="padding: 3rem; text-align: center; color: #6b7280; font-style: italic;">
                Silakan pilih periode tanggal untuk memuat data laporan eksekutif.
            </div>
        @endif
    </div>
</div>
