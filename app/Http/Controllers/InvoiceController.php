<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Http\Resources\InvoiceResource;
use App\Models\CombinedInvoice;
use App\Models\ProformaInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    // List Semua Invoice
    public function index()
    {
        $invoices = Invoice::with(['proformaInvoice.customerInduk', 'delivery'])
            ->latest()
            ->paginate(15);

        return InvoiceResource::collection($invoices);
    }

    // Detail Satu Invoice
    public function show($id)
    {
        $invoice = DB::table('invoices')->where('id', $id)->first();
        if (!$invoice) return response()->json(['message' => 'Not found'], 404);

        $delivery = DB::table('deliveries')->where('id', $invoice->delivery_id)->first();
        $pi = $delivery ? DB::table('proforma_invoices')->where('id', $delivery->proforma_invoice_id)->first() : null;
        $customer = $pi ? DB::table('customer_induks')->where('id', $pi->customer_induk_id)->first() : null;
        $customerBrand = $pi ? DB::table('customer_brands')->where('id', $pi->customer_brand_id)->first() : null;

        // Ambil List Bank untuk pilihan di dropdown
        $available_banks = DB::table('bank_accounts')->select('id', 'bank_name', 'account_number', 'account_holder')->get();

        // Ambil Item
        $items = DB::table('delivery_items')
            ->join('proforma_invoice_items', 'delivery_items.proforma_invoice_item_id', '=', 'proforma_invoice_items.id')
            ->join('products', 'proforma_invoice_items.product_id', '=', 'products.id')
            ->join('units', 'products.unit_id', '=', 'units.id') // Tambahan Join ke Units
            ->where('delivery_items.delivery_id', $invoice->delivery_id)
            ->select(
                'products.name as product_name',
                'units.name as unit_name', // Ambil nama satuan
                'delivery_items.qty_received_good as qty',
                'proforma_invoice_items.unit_price',
                DB::raw('(delivery_items.qty_received_good * proforma_invoice_items.unit_price) as total_item')
            )
            ->get();

        return response()->json([
            'data' => [
                'id' => $invoice->id,
                'status_pembayaran' => $invoice->status,
                'invoice_number' => $invoice->invoice_number,
                'combined_status' => $invoice->is_combined,
                'payment_info' => [
                    'method' => $invoice->payment_method,
                    'bank_account_id' => $invoice->bank_account_id, // Tambahkan ini
                    'proof_url' => $invoice->payment_proof ? url('storage/' . $invoice->payment_proof) : null
                ],
                'customer' => [
                    'name' => $customer->name ?? 'N/A',
                    'address' => $customer->head_office_address ?? '',
                ],
                'customer_brand' => $customerBrand,
                'items' => $items,
                'financials' => [
                    'subtotal' => (float) $items->sum('total_item'),
                    'tax_amount' => (float) $invoice->tax_amount,
                    'total_amount' => (float) $invoice->total_amount,
                ],
                'available_banks' => $available_banks, // Tambahkan list bank ke response
                'notes' => $invoice->notes,
                'data_pesanan' => $pi,
                'data_pengiriman' => $delivery,
            ]
        ]);
    }

    public function cancelCombined(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // ambil data combined_invoice_id dari table pivot
         $combinedInvoiceIds = DB::table('invoice_combined_details')
            ->where('invoice_id', $id)
            ->pluck('combined_invoice_id');

        DB::table('invoice_combined_details')->where('invoice_id', $id)->delete();
        $invoice->update([
            'status' => 'unpaid',
            'is_combined' => 0,
        ]);

        // Cek apakah di table pivot masih ada invoice lain, jika masih ada maka combined invoice tetap ada, jika tidak ada maka combined invoice dihapus
        foreach ($combinedInvoiceIds as $combinedId) {
            $count = DB::table('invoice_combined_details')->where('combined_invoice_id', $combinedId)
                ->count();
            if ($count === 0) {
                CombinedInvoice::destroy($combinedId);
            }
        }

        return response()->json([
            'message' => 'Faktur berhasil dicancel dari Skema Tukar Faktur dan status dikembalikan ke Satu Tagihan.',
            'data' => [
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'is_combined' => $invoice->is_combined,
            ]
        ]);
    }

    // Proses Pembayaran & Upload Bukti
    public function markAsPaid(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $piData = ProformaInvoice::findOrFail($invoice->proforma_invoice_id);

        $isProofExists = $invoice->payment_proof != null;

        $data2 = [
            'payment_method' => $request->payment_method, // Otomatis jadi TRANSFER
            'bank_account_id' => $request->bank_account_id,
        ];

        $invoice->update($data2);

        $request->validate([
            // Payment proof wajib diisi jika di database belum ada file-nya
            'payment_proof' => $isProofExists ? 'nullable|image|max:2048' : 'required|image|max:2048',
            'bank_account_id' => 'required|exists:bank_accounts,id',
        ], [
            'payment_proof.required' => 'Bukti pembayaran wajib diunggah.',
            'bank_account_id.required' => 'Silahkan pilih bank tujuan transfer.',
        ]);

        $data = [
            'status' => 'paid',
            'payment_method' => $request->payment_method, // Otomatis jadi TRANSFER
            'bank_account_id' => $request->bank_account_id,
            'paid_at' => now(),
        ];

        $dataPi = [
            'status' => 'Paid',
        ];

        // 2. Proses Upload Bukti Bayar jika ada file baru
        if ($request->hasFile('payment_proof')) {
            // Hapus foto lama jika ada untuk menghemat storage
            if ($invoice->payment_proof) {
                Storage::disk('public')->delete($invoice->payment_proof);
            }

            // Simpan file baru
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            $data['payment_proof'] = $path;
        }

        // 3. Update data ke tabel invoice
        $invoice->update($data);
        // $piData->update($dataPi);

        return response()->json([
            'message' => 'Pembayaran berhasil dikonfirmasi dan status diperbarui menjadi PAID.',
            'data' => [
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'payment_method' => $invoice->payment_method,
                'proof_url' => $invoice->payment_proof ? asset('storage/' . $invoice->payment_proof) : null
            ]
        ]);
    }

    public function showCombined($id)
    {
        $combined = DB::table('combined_invoices')->where('id', $id)->first();
        if (!$combined) return response()->json(['debug' => 'Route masuk!', 'id_yang_dicari' => $id], 404);

        $invoiceIds = DB::table('invoice_combined_details')
            ->where('combined_invoice_id', $id)
            ->pluck('invoice_id');

        $invoiceDetails = DB::table('invoices')
            ->leftJoin('deliveries', 'invoices.delivery_id', '=', 'deliveries.id')
            ->leftJoin('proforma_invoices', 'deliveries.proforma_invoice_id', '=', 'proforma_invoices.id')
            ->leftJoin('customer_brands', 'proforma_invoices.customer_brand_id', '=', 'customer_brands.id')
            ->whereIn('invoices.id', $invoiceIds)
            ->select(
                'invoices.*',
                // SESUAIKAN 'number' di bawah ini dengan nama kolom asli di tabel deliveries
                'deliveries.*',
                'proforma_invoices.number as pi_number',
                'proforma_invoices.po_number as po_number',
                'customer_brands.*',
                'invoices.id as invoice_id', // Alias untuk ID invoice agar tidak bentrok dengan ID lainnya,
            )
            ->get();

        // 4. Ambil semua Item dari Delivery terkait dengan data Item LENGKAP (SELECT *)
        $deliveryIds = $invoiceDetails->pluck('delivery_id')->filter();

        $allItems = DB::table('delivery_items')
            ->join('proforma_invoice_items', 'delivery_items.proforma_invoice_item_id', '=', 'proforma_invoice_items.id')
            ->join('products', 'proforma_invoice_items.product_id', '=', 'products.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->whereIn('delivery_items.delivery_id', $deliveryIds)
            ->select(
                'delivery_items.*', // MENGAMBIL SEMUA DATA DARI TABLE DELIVERY_ITEMS
                'products.name as product_name',
                'units.name as unit_name',
                'delivery_items.qty_received_good as qty', // Shortcut untuk tampilan
                'proforma_invoice_items.unit_price',
                DB::raw('(delivery_items.qty_received_good * proforma_invoice_items.unit_price) as total_item')
            )
            ->get();

        $allDeliveries = DB::table('deliveries')
            ->whereIn('id', $deliveryIds) // Menggunakan whereIn karena $deliveryIds adalah array
            ->get();

        // 5. Ambil data Customer & Bank
        $customer = DB::table('customer_induks')->where('id', $combined->customer_id)->first();
        $available_banks = DB::table('bank_accounts')->get();

        return response()->json([
            'data' => [
                'id' => $combined->id,
                'combined_number' => $combined->combined_invoice_number,
                'status_pembayaran' => $combined->status,
                'issue_date' => $combined->issue_date,
                'due_date' => $combined->due_date,
                'customer' => [
                    'name' => $customer->name ?? 'N/A',
                    'address' => $customer->head_office_address ?? '',
                ],
                'invoices' => $invoiceDetails,
                'items' => $allItems,
                'deliveries' => $allDeliveries,
                'financials' => [
                    'total_amount' => (float) $combined->total_amount,
                    'subtotal' => (float) $allItems->sum('total_item'),
                ],
                'payment_info' => [
                    'method' => $combined->payment_method,
                    'bank_account_id' => $combined->bank_account_id,
                    'proof_url' => isset($combined->payment_proof) ? url('storage/' . $combined->payment_proof) : null
                ],
                'available_banks' => $available_banks,
                'notes' => $combined->notes,
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'payment_method'    => 'nullable|string',
            'bank_account_id'   => 'nullable|exists:bank_accounts,id',
            'payment_proof'     => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Bukti bayar
        ]);

        try {
            DB::beginTransaction();

            $combined = CombinedInvoice::findOrFail($id);

            $status_pembayaran = $combined->status;
            $proofPath = $combined->payment_proof;

            $proofPath = $combined->payment_proof; // Default pakai yang lama

            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');

                // Cek apakah file valid
                if ($file->isValid()) {
                    // Hapus yang lama jika ada
                    if ($proofPath && Storage::disk('public')->exists($proofPath)) {
                        Storage::disk('public')->delete($proofPath);
                    }

                    // Simpan yang baru
                    $proofPath = $file->store('payments', 'public');
                    $status_pembayaran = 'paid';
                } else {
                    // Ini akan muncul di log jika file corrupt/kosong
                    Log::error("File upload ada tapi tidak valid.");
                }
            } else {
                // Jika file tidak terdeteksi oleh Laravel
                // Tetap gunakan path yang lama agar data di DB tidak terhapus jadi NULL
                $status_pembayaran = ($proofPath) ? 'paid' : 'unpaid';
            }

            $combined->update([
                'status'            => $status_pembayaran,
                'payment_method'    => $request->payment_method ?? $combined->payment_method,
                'bank_account_id'   => $request->bank_account_id ?? $combined->bank_account_id,
                'payment_proof'     => $proofPath,
                'paid_at'           => ($status_pembayaran === 'paid' && !$combined->paid_at) ? now() : $combined->paid_at,
            ]);

            if ($status_pembayaran === 'paid') {
                $combined->invoices()->update(['status' => 'paid', 'bank_account_id' => $request->bank_account_id ?? $combined->bank_account_id, 'payment_method' => $request->payment_method ?? $combined->payment_method, 'paid_at' => now()]);

                $proformaIds = $combined->invoices()->pluck('proforma_invoice_id')->unique();

                \App\Models\ProformaInvoice::whereIn('id', $proformaIds)->update(['status' => 'Paid']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Combined Invoice berhasil diperbarui',
                'data'    => $combined
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }
}
