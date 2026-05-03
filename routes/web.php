<?php

use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PurchaseOrderPrintController;
use App\Models\Invoice;
use App\Models\ProformaInvoice;
use App\Models\Purchase;
use Dompdf\Adapter\PDFLib;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/erp');
});

Route::get('/purchase-orders/{record}/print', PurchaseOrderPrintController::class)
    ->name('purchase-order.print')
    ->middleware(['auth']); // Pasti

Route::get('/purchase/{record}/print', function (Purchase $record) {
    return view('print.purchase-order', compact('record'));
})->name('purchase.print')->middleware(['auth']);

// Print Surat Jalan
Route::get('/delivery/print/{id}', [DeliveryController::class, 'printSj']);

// Print Invoice Biasa 
Route::get('/invoices/{record}/print', function ($record) {
    return view('print.invoice');
})->name('invoice.print')->middleware(['auth']);

// Print Invoice Gabungan 
Route::get('/invoices-combined/{record}/print', function ($record) {
    // Kita kirim variabel $record ke view
    return view('print.invoice-combined', [
        'record' => (object) ['id' => $record]
    ]);
})->name('invoice-combined.print')->middleware(['auth']);


// Download Dokumen Sales Order 
Route::get('/proforma-invoices/{record}/print', function (ProformaInvoice $record) {
    // Ganti 'print.proforma-invoice' dengan lokasi file blade print Anda
    return view('print.proforma-invoice', compact('record'));
})->name('pi.print');
