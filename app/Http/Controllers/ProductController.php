<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function updatePrices(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'record_id' => 'required', // ID Proforma Invoice (misal: 15)
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.hpp_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {

            $totalSubtotalManual = 0;

            // 1. Ambil data PPN dari tabel parent (proforma_invoices)
            $piHeader = DB::table('proforma_invoices')
                ->where('id', $request->record_id)
                ->select('ppn_percent')
                ->first();
            $ppnPercent = $piHeader ? (float)$piHeader->ppn_percent : 0;

            foreach ($request->items as $item) {
                $deliveryDataList = DB::table('delivery_items')
                    ->where('proforma_invoice_item_id', $item['id'])
                    ->get();

                if ($deliveryDataList->isNotEmpty()) {
                    foreach ($deliveryDataList as $deliveryData) {

                        // 1. Ambil data invoice untuk pengecekan status dan combined
                        $invoice = DB::table('invoices')
                            ->where('delivery_id', $deliveryData->delivery_id)
                            ->first();

                        if ($invoice) {
                            // Pengecekan status Paid (Asumsi status disimpan di kolom 'status')
                            // Ganti 'paid' sesuai dengan string status yang Anda gunakan di DB
                            if (strtolower($invoice->status) === 'paid') {
                                return response()->json([
                                    'success' => true,
                                    'message' => "Data tidak bisa di update karena ada FAKTUR yang sudah di bayar (Nomor Faktur: {$invoice->invoice_number})"
                                ],);
                            }

                            // Pengecekan Tukar Faktur (is_combined)
                            if ($invoice->is_combined == 1) {
                                return response()->json([
                                    'success' => true,
                                    'message' => "Data sudah ada yang di TUKAR FAKTUR, tidak bisa di edit harganya (Nomor Faktur: {$invoice->invoice_number})"
                                ],);
                            }

                            // Jika lolos pengecekan, baru reset nilai ke 0
                            DB::table('invoices')
                                ->where('delivery_id', $deliveryData->delivery_id)
                                ->update([
                                    'subtotal'     => 0,
                                    'tax_amount'   => 0,
                                    'total_amount' => 0,
                                    'updated_at'   => now(),
                                ]);
                        }
                    }
                }
            }

            foreach ($request->items as $item) {


                // Ambil data dari delivery_items
                $deliveryDataList = DB::table('delivery_items')
                    ->where('proforma_invoice_item_id', $item['id'])
                    ->get();

                Log::info("Delivery Data Object: " . json_encode($deliveryDataList));

                if ($deliveryDataList->isNotEmpty()) {

                    foreach ($deliveryDataList as $deliveryData) {
                        $totalSubtotalManual = 0;
                        // Update invoice untuk SETIAP delivery_id yang terkait
                        $qtyReceived = $deliveryData ? (float)$deliveryData->qty_received_good : 0;
                        $unitPrice = (float)$item['unit_price'];

                        // Kalkulasi Subtotal Item
                        $itemSubtotal = $qtyReceived * $unitPrice;

                        $ppnAmountItem = ($itemSubtotal * $ppnPercent) / 100;

                        // LOG DETAIL PER ITEM
                        Log::info("--- Kalkulasi Item PI #{$request->record_id} ---");
                        Log::info("Produk ID: {$item['product_id']}");
                        Log::info("Qty Received: {$qtyReceived}");
                        Log::info("Unit Price: {$unitPrice}");
                        Log::info("Item Subtotal: {$itemSubtotal}");
                        Log::info("Item PPN Amount: {$ppnAmountItem}");

                        $totalSubtotalManual += $itemSubtotal + $ppnAmountItem;

                        Log::info("Total Tagihan Baru :  {$totalSubtotalManual}");

                        // Update Table Invoices Kolom subtotal, tax_amount, total_amount
                        DB::table('invoices')
                            ->where('delivery_id', $deliveryData->delivery_id)
                            ->update([
                                'subtotal'     => DB::raw("subtotal + $itemSubtotal"),
                                'tax_amount'   => DB::raw("tax_amount + $ppnAmountItem"),
                                'total_amount' => DB::raw("total_amount + $totalSubtotalManual"),
                                'updated_at'   => now(),
                            ]);

                        // LOG NILAI QTY KE LARAVEL LOG
                        Log::info("Update PI #{$request->record_id}: Product ID {$item['product_id']} memiliki QTY Received = {$qtyReceived}");
                    }
                } else {
                    Log::info("Deliveri Data gak ada");
                }
                // Hitung subtotal baru berdasarkan qty dari delivery log tersebut
                $qtyNumerik = isset($item['qty']) ? (float) str_replace(',', '.', $item['qty']) : 0;

                $newSubtotal = $qtyNumerik * $item['unit_price'];

                // Update record di proforma_invoice_items sesuai struktur SQL
                DB::table('proforma_invoice_items')
                    ->where('proforma_invoice_id', $request->record_id)
                    ->where('product_id', $item['product_id'])
                    ->update([
                        'unit_price' => $item['unit_price'],
                        'hpp_price'  => $item['hpp_price'],
                        'subtotal'   => $newSubtotal,
                        'updated_at' => now(),
                    ]);
            }

            // Update total keseluruhan di parent table
            // 1. Hitung total amount terbaru dari items
            $totalAmount = DB::table('proforma_invoice_items')
                ->where('proforma_invoice_id', $request->record_id)
                ->sum('subtotal');

            // 2. Kalkulasi PPN Amount dan Grand Total

            $ppnAmount = ($totalAmount * $ppnPercent) / 100;
            $grandTotal = $totalAmount + $ppnAmount;

            // 3. Update tabel parent dengan data lengkap
            DB::table('proforma_invoices')
                ->where('id', $request->record_id)
                ->update([
                    'total_amount' => $totalAmount,
                    'ppn_amount'   => $ppnAmount,
                    'grand_total'  => $grandTotal,
                    'updated_at'   => now(),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memperbarui harga dan kalkulasi subtotal PI #' . $request->record_id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal Update Proforma Invoice Items: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
