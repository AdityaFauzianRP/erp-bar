<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseOrderPrintController extends Controller
{
    public function __invoke(Purchase $record)
    {
        // Ambil data yang diperlukan agar view tidak berat
        return view('print.purchase-order', [
            'record' => $record,
            'supplier' => $record->supplier,
            'items' => $record->items, // Asumsi relasi ke PurchaseItem
        ]);
    }
}