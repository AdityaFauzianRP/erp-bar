<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB; // PENTING: Jangan lupa ini
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function checkStock(Request $request)
    {
        try {
            $warehouseId = $request->query('warehouse_id');
            $productId = $request->query('product_id');

            // Cek apakah parameter ada
            if (!$warehouseId || !$productId) {
                return response()->json(['stock' => 0, 'message' => 'Missing parameters'], 400);
            }

            // Query ke database
            // PASTIKAN: nama tabel 'stocks', kolom 'warehouse_id', 'product_id', dan 'qty' benar
            $stock = DB::table('inventories') 
                ->where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->value('stock');

            return response()->json([
                'status' => 'success',
                'stock' => ($stock ?? 0)
            ]);

        } catch (\Exception $e) {
            // Ini akan mencatat detail error di storage/logs/laravel.log
            Log::error("Stock Check Error: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}