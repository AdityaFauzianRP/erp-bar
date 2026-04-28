<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use App\Models\CustomerBrand;
use App\Models\CustomerInduk;
use App\Models\CustomerProductPrice;
use App\Models\ProformaInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Route untuk ambil Brand berdasarkan Customer Induk
Route::get('/customer-brands/{indukId}', function ($indukId) {
    return CustomerBrand::where('customer_induk_id', $indukId)
        ->select('id', 'brand_name as name', 'nama_cabang', 'kota_cabang') // Kita alias-kan supaya Alpine tetap baca 'name'
        ->get();
});

// Route untuk ambil Harga Spesial (jika ada)
Route::get('/get-special-price/{indukId}/{productId}', function ($indukId, $productId) {
    $specialPrice = CustomerProductPrice::where('customer_induk_id', $indukId)
        ->where('product_id', $productId)
        ->first();

    return response()->json([
        'price' => $specialPrice ? $specialPrice->price : null
    ]);
});

Route::get('/customer-products/{indukId}', function ($indukId) {
    return \App\Models\CustomerProductPrice::where('customer_induk_id', $indukId)
        ->join('products', 'customer_product_prices.product_id', '=', 'products.id')
        ->leftJoin('units', 'products.unit_id', '=', 'units.id')
        ->select(
            'products.id',
            'products.name',
            'products.hpp', // <--- WAJIB TAMBAH INI
            'units.name as unit',
            'customer_product_prices.special_price as price'
        )
        ->get();
});


// api create PI
Route::post('/proforma-invoices/store', function (Request $request) {
    
    return DB::transaction(function () use ($request) {
        // 1. Simpan Header dengan konversi tipe data yang aman
        $pi = ProformaInvoice::create([
            'number'            => $request->number,
            'date'              => $request->date,
            'customer_induk_id' => $request->customer_induk_id,
            'customer_brand_id' => $request->customer_brand_id,
            'po_number'         => $request->po_number,
            'delivery_deadline' => $request->delivery_deadline,
            'due_date'          => $request->due_date,
            'notes'             => $request->notes,
            'status'            => 'Created',

            // Gunakan (float) atau (double) untuk memastikan angka masuk ke DB
            'total_amount'      => (float) $request->total_amount,
            'ppn_percent'       => (float) ($request->ppn_percent ?? 0),
            'ppn_amount'        => (float) ($request->ppn_amount ?? 0),
            'grand_total'       => (float) ($request->grand_total ?? $request->total_amount),
        ]);

        // 2. Simpan Items
        foreach ($request->items as $item) {
            $pi->items()->create([
                'product_id' => $item['product_id'],
                'qty'        => (float) $item['qty'],
                'unit_price' => (float) $item['unit_price'],
                'subtotal'   => (float) $item['subtotal'],
                'hpp_price'  => (float) $item['hpp_price'], // Pastikan hpp_price juga disimpan
            ]);
        }

        return response()->json(['success' => true, 'id' => $pi->id]);
    });
});


Route::get('/proforma-invoices/{id}', function ($id) {
    try {
        $pi = ProformaInvoice::with(['items.product', 'customerInduk', 'customerBrand'])->find($id);

        if (!$pi) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // customer induk dan brand di get all data
        $customerInduk = CustomerInduk::find($pi->customer_induk_id);
        $customerBrand = CustomerBrand::find($pi->customer_brand_id);

        return response()->json([
            'success' => true,
            'data' => [
                'id'                => $pi->id,
                'number'            => $pi->number,
                'po_number'         => $pi->po_number,
                'customer_induk_id' => $pi->customer_induk_id,
                'customer_brand_id' => $pi->customer_brand_id,
                'date'              => $pi->date,
                'delivery_deadline' => $pi->delivery_deadline,
                'due_date'          => $pi->due_date,
                'notes'             => $pi->notes,

                // customer induk dan brand di get all data
                'customer_induk'    => $customerInduk,
                'customer_brand'    => $customerBrand,

                // Tambahkan field PPN di sini
                'ppn_percent'       => $pi->ppn_percent ?? 11, // Default 11 jika null
                'ppn_amount'        => $pi->ppn_amount,
                'subtotal'          => $pi->subtotal, // Pastikan ada subtotal (DPP)
                'total_amount'      => $pi->total_amount, // Ini Grand Total

                'items' => $pi->items->map(function ($item) {
                    return [
                        'product_name' => $item->product?->name,
                        'product_id' => $item->product_id,
                        'qty'        => $item->qty,
                        'unit_price' => $item->unit_price,
                        'subtotal'   => $item->subtotal,
                        'unit'       => $item->product?->unit
                    ];
                })
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});


// API EDIT INVOICE
Route::put('/proforma-invoices/{id}', function (Request $request, $id) {
    try {
        return DB::transaction(function () use ($request, $id) {
            $pi = ProformaInvoice::findOrFail($id);

            // Bungkus semua data input ke dalam array untuk kemudahan
            $inputData = [
                'number'            => $request->number,
                'po_number'         => $request->po_number,
                'customer_induk_id' => $request->customer_induk_id,
                'customer_brand_id' => $request->customer_brand_id,
                'date'              => $request->date,
                'delivery_deadline' => $request->delivery_deadline,
                'due_date'          => $request->due_date,
                'notes'             => $request->notes,
                'subtotal'          => $request->subtotal,
                'ppn_percent'       => $request->ppn_percent,
                'ppn_amount'        => $request->ppn_amount,
                'total_amount'      => $request->total_amount,
                'items'             => $request->items, // Sertakan array items di dalam JSON
            ];

            // KONDISI 1: Jika sudah 'approved', jangan ubah data asli, masukkan ke pending_changes
            if ($pi->status === 'Created') {
                $inputData['status'] = 'draft'; // Set status baru untuk revisi

                $pi->update([
                    'pending_changes' => $inputData
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Perubahan disimpan sebagai draft revisi dan menunggu approval.'
                ]);
            }

            // KONDISI 2: Jika masih 'draft', update seperti biasa (Logic Lama Anda)
            $pi->update(collect($inputData)->except('items')->toArray());

            // Sync Items
            $pi->items()->delete();
            foreach ($request->items as $item) {
                if (!empty($item['product_id'])) {
                    $pi->items()->create([
                        'product_id' => $item['product_id'],
                        'qty'        => $item['qty'],
                        'unit_price' => $item['unit_price'],
                        'hpp_price'  => $item['hpp_price'],
                        'subtotal'   => $item['subtotal'],
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Data updated successfully']);
        });
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal memperbarui data: ' . $e->getMessage()
        ], 500);
    }
});


//  API APPROVE PI
Route::put('/proforma-invoices/{id}/approve', function ($id) {
    try {
        return DB::transaction(function () use ($id) {
            $pi = ProformaInvoice::findOrFail($id);

            // 1. CEK: Apakah ada revisi yang menunggu di kolom JSON?
            if (!empty($pi->pending_changes)) {
                $newData = $pi->pending_changes;

                // A. Update Header (ambil semua kecuali items dari JSON)
                $headerData = collect($newData)->except('items')->toArray();
                $headerData['status'] = 'Created'; // Pastikan status tetap Created
                $headerData['pending_changes'] = null; // Kosongkan antrian karena sudah disetujui

                $pi->update($headerData);

                // B. Update Items (Hapus yang lama, ganti dengan yang dari JSON)
                if (isset($newData['items'])) {
                    $pi->items()->delete();
                    foreach ($newData['items'] as $item) {
                        if (!empty($item['product_id'])) {
                            $pi->items()->create([
                                'product_id' => $item['product_id'],
                                'qty'        => $item['qty'],
                                'unit_price' => $item['unit_price'],
                                'hpp_price'  => $item['hpp_price'] ?? 0,
                                'subtotal'   => $item['subtotal'],
                            ]);
                        }
                    }
                }

                $message = "Revisi PI berhasil disetujui dan data diperbarui.";
            } else {
                // 2. JIKA TIDAK ADA REVISI (Approve biasa dari Draft)
                $pi->update(['status' => 'Created']);
                $message = "PI Berhasil di-approve.";
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        });
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal: ' . $e->getMessage()
        ], 500);
    }
});



// API Modul Delivery
Route::get('/delivery/pi-detail/{id}', [DeliveryController::class, 'getPiDetailForDelivery']);
Route::get('/delivery/{id}', [DeliveryController::class, 'show']);
Route::post('/delivery/{id}/complete', [DeliveryController::class, 'completeDelivery']);
Route::post('/delivery', [DeliveryController::class, 'storeDelivery']);
Route::post('/delivery/{id}', [DeliveryController::class, 'update']);


// API Cek Stok Barang Di Gudang 
Route::get('/inventory/check-stock', [InventoryController::class, 'checkStock']);

Route::prefix('invoices')->group(function () {
    Route::get('/', [InvoiceController::class, 'index']);

    Route::post('/{id}/pay', [InvoiceController::class, 'markAsPaid']);

    Route::post('/{id}/cancel-combined', [InvoiceController::class, 'cancelCombined']);
    // showCombined
    Route::get('/showCombined/{id}', [InvoiceController::class, 'showCombined']);
    Route::post('/combined-invoices/{id}', [InvoiceController::class, 'update']);
    Route::get('/{id}', [InvoiceController::class, 'show']);
});

Route::post('/update-product-prices', [ProductController::class, 'updatePrices']);
