<?php

namespace App\Filament\Resources\Purchases\Pages;

use App\Filament\Resources\Purchases\PurchaseResource;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EditPurchase extends EditRecord
{
    protected static string $resource = PurchaseResource::class;
    protected string $view = 'purchases.edit-purchase';

    public $search = '';
    public $selectedProductIds = [];

    public $record_id, $supplier_id, $notes, $tax_rate, $created_at;
    public $items = [];
    public $subtotal = 0, $tax = 0, $grand_total = 0;


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $transaction = \App\Models\Transaction::where('purchase_id', $this->record->id)->first();

        // Jika kategorinya PO-DIRECT, paksa pindah ke halaman DirectPurchase
        if ($transaction && $transaction->kategori === 'PO-DIRECT') {
            redirect()->to(
                \App\Filament\Resources\Purchases\PurchaseResource::getUrl('direct-edit', ['record' => $this->record->id])
            );
        }

        $purchase = Purchase::with(['items.product.unit'])->findOrFail($record);

        $this->record_id = $purchase->id;
        $this->supplier_id = $purchase->supplier_id;
        $this->notes = $purchase->notes;
        $this->tax_rate = $purchase->tax_rate ?? 0;
        $this->created_at = $purchase->created_at->format('Y-m-d');

        foreach ($purchase->items as $detail) {
            $this->items[] = [
                'product_id' => $detail->product_id,
                'name'       => $detail->product->name ?? 'Produk Terhapus',
                'code'       => $detail->product->code ?? '-',
                'price'      => (float) $detail->unit_price,
                'qty'        => (float) $detail->quantity,
                'unit'       => $detail->product->unit->name ?? '-',
                'subtotal'   => (float) $detail->subtotal,
            ];
        }
        $this->calculateTotals();
    }

    // Mengupdate total otomatis saat pajak atau supplier berubah
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['tax_rate', 'supplier_id'])) {
            $this->calculateTotals();
        }
    }

    // public function calculateTotals()
    // {
    //     $this->subtotal = collect($this->items)->sum(fn($item) => $item['price'] * $item['qty']);
    //     $rate = (float) $this->tax_rate;
    //     $this->tax = $this->subtotal * ($rate / 100);
    //     $this->grand_total = $this->subtotal + $this->tax;
    // }

    public function addItem($productId)
    {
        $product = Product::with('unit')->find($productId);
        if (!$product) return;

        $exists = false;
        foreach ($this->items as $index => $item) {
            if ($item['product_id'] == $productId) {
                $this->items[$index]['qty']++;
                $this->updateQty($index, $this->items[$index]['qty']);
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            // Ambil harga beli khusus dari supplier yang dipilih
            $harga = DB::table('product_supplier')
                ->where('product_id', $productId)
                ->where('supplier_id', $this->supplier_id)
                ->value('harga_beli_khusus') ?? 0;

            $this->items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'unit' => $product->unit->name ?? '-',
                'price' => (float)$harga,
                'qty' => 1,
                'subtotal' => (float)$harga,
            ];
        }

        $this->calculateTotals();
    }

    public function addSelectedProducts()
    {
        foreach ($this->selectedProductIds as $productId) {
            $this->addItem($productId);
        }
        $this->selectedProductIds = [];
        $this->dispatch('close-modal', id: 'search-modal');
    }

    public function updateQty($index, $qty)
    {
        // Ubah koma ke titik agar dipahami PHP sebagai desimal
        $cleanQty = str_replace(',', '.', $qty);

        // Gunakan float dan minimal 0.01 agar tidak nol
        $this->items[$index]['qty'] = max(0.01, (float)$cleanQty);

        // Hitung ulang subtotal baris
        $this->items[$index]['subtotal'] = $this->items[$index]['qty'] * $this->items[$index]['price'];

        $this->calculateTotals();
    }

    public function updatePrice($index, $price)
    {
        // Bersihkan karakter non-numerik kecuali titik desimal
        // (Jika input dari Blade menggunakan koma, ubah dulu ke titik)
        $valueWithDot = str_replace(',', '.', $price);
        $cleanPrice = preg_replace('/[^0-9.]/', '', $valueWithDot);

        if (isset($this->items[$index])) {
            $this->items[$index]['price'] = (float) $cleanPrice;
            $this->items[$index]['subtotal'] = $this->items[$index]['qty'] * (float) $cleanPrice;
        }

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->items as $index => $item) {
            // Hitung ulang tiap baris untuk memastikan sinkronisasi
            $lineTotal = (float)$item['price'] * (float)$item['qty'];
            $this->items[$index]['subtotal'] = $lineTotal;

            $this->subtotal += $lineTotal;
        }

        $rate = (float) $this->tax_rate;
        $this->tax = $this->subtotal * ($rate / 100);
        $this->grand_total = $this->subtotal + $this->tax;
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        if (empty($this->items)) {
            Notification::make()->title('Daftar barang tidak boleh kosong')->danger()->send();
            return;
        }

        DB::transaction(function () {
            $purchase = $this->getRecord();

            $tanggalInput = $this->created_at ?: now()->format('Y-m-d');

            $finalTimestamp = $tanggalInput . ' ' . now()->format('H:i:s');

            $purchase->update([
                'supplier_id' => $this->supplier_id,
                'tax_rate'    => $this->tax_rate,
                'tax_amount'  => $this->tax,
                'subtotal'    => $this->subtotal,
                'grand_total' => $this->grand_total,
                'notes'       => $this->notes,
                'edited_by'   => auth()->id(),
                'status'      => 'Menunggu Approval', // Reset status jika diedit
                'approved_by' => null,
                'approved_at' => null,
                'created_at'  => $finalTimestamp,
            ]);

            $purchase->items()->delete();
            foreach ($this->items as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['qty'],
                    'unit_price' => $item['price'],
                    'subtotal'   => $item['subtotal'],
                ]);
            }
        });

        if ($shouldSendSavedNotification) {
            Notification::make()->title('Perubahan PO Disimpan')->success()->send();
        }

        if ($shouldRedirect) {
            $this->redirect($this->getRedirectUrl());
        }
    }

    public function openApproveModal()
    {
        $this->dispatch('open-modal', id: 'confirm-approve-modal');
    }

    public function approve()
    {
        DB::transaction(function () {
            $purchase = Purchase::findOrFail($this->record_id);
            $purchase->update([
                'status' => 'Ordered',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        Notification::make()->title('PO Berhasil Disetujui')->success()->send();
        return redirect($this->getResource()::getUrl('index'));
    }

    public function printPO()
    {
        $url = route('purchase.print', $this->record_id);
        $this->dispatch('trigger-print', url: $url);
    }
}
