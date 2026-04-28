<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProformaInvoice;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    public function getPiDetailForDelivery($id)
    {
        try {
            $pi = ProformaInvoice::with([
                'customerBrand.customerInduk',
                'items.product'
            ])->find($id);

            if (!$pi) {
                return response()->json(['message' => 'Data Proforma Invoice tidak ditemukan.'], 404);
            }

            $items = $pi->items->map(function ($item) {
                // 1. Total yang SUDAH DITERIMA dengan baik (delivered)
                $totalReceivedGood = DB::table('delivery_items')
                    ->join('deliveries', 'delivery_items.delivery_id', '=', 'deliveries.id')
                    ->where('delivery_items.proforma_invoice_item_id', $item->id)
                    ->where('deliveries.status', 'delivered')
                    ->sum('qty_received_good') ?? 0;

                // 2. Total yang SEDANG DALAM PROSES (Draft atau shipping)
                $totalOnProgress = DB::table('delivery_items')
                    ->join('deliveries', 'delivery_items.delivery_id', '=', 'deliveries.id')
                    ->where('delivery_items.proforma_invoice_item_id', $item->id)
                    ->whereIn('deliveries.status', ['Draft', 'shipping'])
                    ->sum('qty_shipped') ?? 0;

                // 3. Hitung Sisa Kuota Riil
                // Rumus: Qty Order - (Total Diterima Bagus + Total Sedang Jalan)
                $sisaKuota = ($item->qty ?? 0) - ($totalReceivedGood + $totalOnProgress);

                return [
                    'pi_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'Produk Tanpa Nama',
                    'qty_order' => ($item->qty ?? 0),
                    'qty_received_good' => $totalReceivedGood, // Info tambahan untuk UI
                    'qty_on_progress' => $totalOnProgress,   // Info tambahan untuk UI
                    'qty_sisa_kirim' => max(0, $sisaKuota),  // Gunakan max(0) agar tidak negatif
                    'unit_price' => $item->unit_price ?? 0,
                    'warehouse_id' => '',
                ];
            });

            return response()->json([
                'pi_number'     => $pi->number,
                'customer_id'   => $pi->customer_brand_id,
                'customer_name' => $pi->customerBrand->customerInduk->name ?? 'N/A',
                'brand_name'    => $pi->customerBrand->brand_name ?? '-',
                'nama_cabang'   => $pi->customerBrand->nama_cabang ?? '-',
                'items'         => $items
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan internal.',
                'debug_error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $delivery = Delivery::with([
            'items.piItem.product',
            'proformaInvoice.customerBrand.customerInduk'
        ])->findOrFail($id);

        $delivery->items->transform(function ($item) use ($delivery) {
            // 1. Total yang SUDAH SAMPAI (delivered) dari SJ manapun
            // 1. Total yang SUDAH DELIVERED
            $totalReceivedGood = DB::table('delivery_items')
                ->join('deliveries', 'delivery_items.delivery_id', '=', 'deliveries.id')
                ->where('delivery_items.proforma_invoice_item_id', $item->proforma_invoice_item_id)
                ->where('deliveries.status', 'delivered')
                ->sum('qty_received_good');

            // 2. Total yang SEDANG PROSES (Termasuk SJ ini)
            $totalOnProgress = DB::table('delivery_items')
                ->join('deliveries', 'delivery_items.delivery_id', '=', 'deliveries.id')
                ->where('delivery_items.proforma_invoice_item_id', $item->proforma_invoice_item_id)
                ->whereIn('deliveries.status', ['Draft', 'shipping'])
                ->sum('qty_shipped');

            $qtyOrderPI = $item->piItem->qty ?? 0;
            $sisaKuota = $qtyOrderPI - ($totalReceivedGood + $totalOnProgress);

            // LOGGER UNTUK DEBUGGING
            Log::info("Debug Kuota PI Item ID: " . $item->proforma_invoice_item_id, [
                'Nomor_SJ_Sekarang' => $delivery->id,
                'Qty_Order_PI'      => $qtyOrderPI,
                'Total_Delivered'   => $totalReceivedGood,
                'Total_On_Progress' => $totalOnProgress,
                'Hasil_Sisa'        => $sisaKuota,
            ]);

            $item->qty_sisa_kirim = $sisaKuota;   // Contoh: 10 - (1 + 9) = 0

            // Atribut ini untuk membatasi inputan di UI (Max allowed adalah sisa kuota)
            $item->qty_max_allowed = $sisaKuota;

            return $item;
        });

        return response()->json($delivery);
    }
    /**
     * PROSES SELESAI PENGIRIMAN & POTONG STOK
     */
    public function completeDelivery(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:delivery_items,id',

            // Perbaikan: Wajib diisi, harus angka, dan minimal 0.1
            'items.*.qty_received_good' => [
                'required_without:items.*.qty_wasted', // Wajib jika wasted kosong
                'numeric',
                'min:0', // Pakai 0 jika boleh kosong, atau sesuaikan logicmu
            ],
            'items.*.qty_wasted' => [
                'required_without:items.*.qty_received_good', // Wajib jika received kosong
                'numeric',
                'min:0',
            ],

            'items.*.qty_wasted' => 'min:0',
        ], [
            // Custom message agar pesan error lebih mudah dipahami user
            'items.*.qty_received_good.required' => 'Jumlah barang bagus wajib diisi.',
        ]);

        return DB::transaction(function () use ($request, $id) {
            // Ambil data delivery beserta data PI induknya untuk dapat PPN Percent
            $delivery = Delivery::findOrFail($id);
            $piHeader = DB::table('proforma_invoices')->where('id', $delivery->proforma_invoice_id)->first();

            if ($delivery->status === 'delivered') {
                throw new \Exception("Pengiriman ini sudah berstatus DELIVERED.");
            }

            $subtotalInvoice = 0;

            foreach ($request->items as $itemData) {
                $item = DeliveryItem::findOrFail($itemData['id']);

                // 1. Update data item pengiriman
                $item->update([
                    'qty_received_good' => $itemData['qty_received_good'],
                    'qty_wasted' => $itemData['qty_wasted'],
                ]);

                // 2. HITUNG SUB TOTAL
                $piItem = DB::table('proforma_invoice_items')->where('id', $item->proforma_invoice_item_id)->first();

                if ($piItem) {
                    // Hanya menagih yang diterima dengan kondisi baik
                    $itemTotalHarga = $itemData['qty_received_good'] * $piItem->unit_price;
                    $subtotalInvoice += $itemTotalHarga;
                }
            }

            // 3. Update status Delivery
            $delivery->update(['status' => 'delivered']);

            // -----------------------------------------------------------
            // LOGIC GENERATE INVOICE DENGAN PPN DINAMIS
            // -----------------------------------------------------------

            // Ambil percent PPN dari PI, jika null atau tidak ada set ke 0
            $piHeader = DB::table('proforma_invoices')
                ->where('id', $delivery->proforma_invoice_id)
                ->first();

            if (!$piHeader) {
                throw new \Exception("Data Proforma Invoice tidak ditemukan.");
            }

            $ppnPercent = $piHeader->ppn_percent ?? 0;

            $dueDate = $piHeader->due_date ? $piHeader->due_date : now()->addDays(7);

            // Hitung nominal pajak: (Subtotal * Percent) / 100
            $taxAmount = ($subtotalInvoice * $ppnPercent) / 100;
            $totalAmount = $subtotalInvoice + $taxAmount;

            $year2Digit = date('y');
            $month = date('m');
            $day = date('d');
            $prefix = "FR{$year2Digit}{$month}{$day}";

            // 1. Ambil invoice terakhir dengan prefix hari ini
            $lastInvoice = Invoice::where('invoice_number', 'like', $prefix . '%')
                ->orderBy('invoice_number', 'desc')
                ->first();

            if (!$lastInvoice) {
                $nextCounter = 1;
            } else {
                // Mengambil 4 digit terakhir
                $lastCounter = (int) substr($lastInvoice->invoice_number, -4);
                $nextCounter = $lastCounter + 1;
            }

            // 2. PROTEKSI TERAKHIR: Loop pengecekan
            do {
                $counter = str_pad($nextCounter, 4, '0', STR_PAD_LEFT);
                $invoiceNumber = $prefix . $counter;

                $exists = Invoice::where('invoice_number', $invoiceNumber)->exists();
                if ($exists) {
                    $nextCounter++;
                }
            } while ($exists);

            // 3. SELESAI. Langsung gunakan $invoiceNumber untuk insert.
            // JANGAN tulis ulang $invoiceNumber = ... di sini lagi.

            $invoice = Invoice::create([
                'invoice_number'      => $invoiceNumber,
                'delivery_id'         => $delivery->id,
                'proforma_invoice_id' => $delivery->proforma_invoice_id,
                'invoice_date'        => now(),
                'due_date'            => $dueDate,
                'subtotal'            => $subtotalInvoice,
                'tax_amount'          => $taxAmount,
                'total_amount'        => $totalAmount,
                'status'              => 'unpaid',
                'payment_method'      => 'CASH',
            ]);

            // -----------------------------------------------------------
            // CEK STATUS PI (Sesuai Logic Kamu Sebelumnya)
            // -----------------------------------------------------------
            $piId = $delivery->proforma_invoice_id;
            $allDeliveriesForPi = Delivery::where('proforma_invoice_id', $piId)->get();
            $anyPendingDelivery = $allDeliveriesForPi->whereIn('status', ['draft', 'shipping'])->count();

            if ($anyPendingDelivery === 0) {
                $piItems = DB::table('proforma_invoice_items')->where('proforma_invoice_id', $piId)->get();
                $isAllItemsFullfilled = true;

                foreach ($piItems as $piItem) {
                    $totalReceivedGood = DB::table('delivery_items')
                        ->join('deliveries', 'delivery_items.delivery_id', '=', 'deliveries.id')
                        ->where('delivery_items.proforma_invoice_item_id', $piItem->id)
                        ->where('deliveries.status', 'delivered')
                        ->sum('qty_received_good');

                    if ($totalReceivedGood < $piItem->qty) {
                        $isAllItemsFullfilled = false;
                        break;
                    }
                }

                if ($isAllItemsFullfilled) {
                    DB::table('proforma_invoices')->where('id', $piId)->update(['status' => 'Delivered']);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Delivery completed and Invoice generated.',
                'data' => [
                    'invoice_number' => $invoice->invoice_number,
                    'subtotal' => $subtotalInvoice,
                    'ppn_percent' => $ppnPercent . '%',
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount
                ]
            ]);
        });
    }

    /**
     * PROSES SIMPAN SURAT JALAN (CREATE)
     */
    public function storeDelivery(Request $request)
    {
        $request->validate([
            'proforma_invoice_id' => 'required|exists:proforma_invoices,id',
            'delivery_date'       => 'required|date',
            'items'               => 'required|array|min:1',
            'items.*.proforma_invoice_item_id' => 'required|exists:proforma_invoice_items,id',
            'items.*.qty_shipped' => 'min:0',
            'items.*.warehouse_id' => 'required|exists:warehouses,id',
        ]);

        try {
            return DB::transaction(function () use ($request) {

                // --- LOGIKA GENERATE NO SJ OTOMATIS ---
                $datePrefix = now()->format('ymd'); // Hasil: 260221
                $currentMonth = now()->format('m');
                $currentYear = now()->format('Y');

                // Cari nomor terakhir di bulan ini (gunakan lockForUpdate agar tidak double nomor)
                $lastDelivery = Delivery::whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $currentMonth)
                    ->lockForUpdate()
                    ->latest('id')
                    ->first();

                $counter = $lastDelivery ? ((int) substr($lastDelivery->no_sj, -4)) + 1 : 1;
                $newNoSJ = "SJ" . $datePrefix . str_pad($counter, 4, '0', STR_PAD_LEFT);
                // --- END LOGIKA ---

                $delivery = Delivery::create([
                    'no_sj'               => $newNoSJ, // Gunakan nomor yang digenerate
                    'proforma_invoice_id' => $request->proforma_invoice_id,
                    'delivery_date'       => $request->delivery_date,
                    'driver_name'         => $request->driver_name,
                    'vehicle_plate'       => $request->vehicle_plate,
                    'status'              => 'shipping',
                    'notes'               => $request->notes,
                ]);

                foreach ($request->items as $itemData) {
                    $piItem = DB::table('proforma_invoice_items')->where('id', $itemData['proforma_invoice_item_id'])->first();

                    $totalReceivedGood = DB::table('delivery_items')
                        ->join('deliveries', 'delivery_items.delivery_id', '=', 'deliveries.id')
                        ->where('delivery_items.proforma_invoice_item_id', $itemData['proforma_invoice_item_id'])
                        ->where('deliveries.status', 'delivered')
                        ->sum('qty_received_good');

                    $totalOnProgress = DB::table('delivery_items')
                        ->join('deliveries', 'delivery_items.delivery_id', '=', 'deliveries.id')
                        ->where('delivery_items.proforma_invoice_item_id', $itemData['proforma_invoice_item_id'])
                        ->whereIn('deliveries.status', ['Draft', 'shipping'])
                        ->where('deliveries.id', '!=', $delivery->id) // Exclude data yang lagi di-edit
                        ->sum('qty_shipped');

                    $sisaKuota = $piItem->qty - ($totalReceivedGood + $totalOnProgress);

                    // if ($itemData['qty_shipped'] > $sisaKuota) {
                    //     throw new \Exception("Stok PI tidak cukup. Sisa kuota: {$sisaKuota}. (Diterima sebelumnya: {$totalReceivedGood}, Sedang jalan: {$totalOnProgress})");
                    // }

                    $inventory = DB::table('inventories')
                        ->where('warehouse_id', $itemData['warehouse_id'])
                        ->where('product_id', $piItem->product_id)
                        ->lockForUpdate()
                        ->first();

                    // $currentQty = $inventory->stock ?? 0;

                    // // if (!$inventory || $currentQty < $itemData['qty_shipped']) {
                    // //     $productName = DB::table('products')->where('id', $piItem->product_id)->value('name') ?? "ID: " . $piItem->product_id;
                    // //     throw new \Exception("Stok kurang di gudang untuk: {$productName}. (Tersedia: {$currentQty})");
                    // // }

                    DB::table('inventories')
                        ->where('warehouse_id', $itemData['warehouse_id'])
                        ->where('product_id', $piItem->product_id)
                        ->decrement('stock', $itemData['qty_shipped']);

                    DeliveryItem::create([
                        'delivery_id'              => $delivery->id,
                        'proforma_invoice_item_id' => $itemData['proforma_invoice_item_id'],
                        'qty_shipped'              => $itemData['qty_shipped'],
                        'warehouse_id'             => $itemData['warehouse_id'],
                        'qty_received_good'        => 0,
                        'qty_wasted'               => 0,
                    ]);
                }

                // Update Status PI
                DB::table('proforma_invoices')
                    ->where('id', $request->proforma_invoice_id)
                    ->update(['status' => 'Delivery Created']);

                return response()->json([
                    'message' => 'Surat Jalan berhasil dibuat!',
                    'no_sj' => $newNoSJ
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function printSj($id)
    {
        // Load data dengan relasi yang dibutuhkan sesuai JSON yang Anda berikan tadi
        $record = Delivery::with([
            'proforma_invoice.customer_brand.customer_induk',
            'items.pi_item.product.unit'
        ])->findOrFail($id);

        // Sesuaikan dengan nama file di screenshot: surat-jalan.blade.php di folder print
        return view('print.surat-jalan', compact('record'));
    }


    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $delivery = Delivery::findOrFail($id);

            $delivery->update([
                'driver_name'   => $request->driver_name,
                'vehicle_plate' => $request->vehicle_plate,
                'delivery_date' => $request->delivery_date,
                'notes'         => $request->notes,
                'status'        => 'shipping',
            ]);

            $delivery->items()->delete();

            foreach ($request->items as $item) {
                $delivery->items()->create([
                    'delivery_id'              => $delivery->id,
                    'proforma_invoice_item_id' => $item['proforma_invoice_item_id'],
                    'product_id'               => $item['product_id'],
                    'qty_shipped'              => $item['qty_shipped'],
                    'warehouse_id'             => $item['warehouse_id'],
                    'unit_price'               => $item['unit_price'] ?? 0,
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Delivery updated successfully', 'data' => $delivery], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal Update: ' . $e->getMessage()], 500);
        }
    }
}
