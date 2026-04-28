<?php

namespace App\Filament\Resources\Purchases\Pages;

use App\Filament\Resources\Purchases\PurchaseResource;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class CreatePurchase extends CreateRecord implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = PurchaseResource::class;
    protected string $view = 'purchases.create-purchase';

    public $search = '';
    public $selectedProductIds = [];
    public $supplier_id;
    public $notes;
    public $is_tax = false;
    public $items = []; // Disinkronkan dengan Alpine via @entangle
    public $tax_rate = 0;
    public $created_at;
    public $due_date;

    public function mount(): void
    {
        parent::mount();
        $this->created_at = now()->format('Y-m-d');
    }

    public function addItem($productId)
    {
        if (!$this->supplier_id) {
            $this->dispatch('close-modal', id: 'search-modal');
            Notification::make()->title('Pilih Supplier dulu!')->danger()->send();
            return;
        }

        $product = \App\Models\Product::find($productId);

        // Cek duplikasi
        foreach ($this->items as $index => $item) {
            if ($item['product_id'] == $productId) {
                $this->items[$index]['qty']++;
                return;
            }
        }

        $harga = $product->suppliers()->where('supplier_id', $this->supplier_id)->first()?->pivot->harga_beli_khusus ?? 0;

        // Push ke array (Alpine akan otomatis mendeteksi perubahan ini)
        $this->items[] = [
            'product_id' => $product->id,
            'name'       => $product->name,
            'code'       => $product->code,
            'unit'       => $product->unit->name ?? '-',
            'price'      => (float)$harga,
            'qty'        => 1,
        ];
    }

    public function addSelectedProducts()
    {
        foreach ($this->selectedProductIds as $productId) {
            $this->addItem($productId);
        }
        $this->selectedProductIds = [];
        $this->dispatch('close-modal', id: 'search-modal');
    }

    public function save()
    {
        if (empty($this->items)) {
            Notification::make()->title('Barang masih kosong!')->danger()->send();
            return;
        }

        DB::transaction(function () {
            // Re-calculate di server untuk validasi final
            $subtotal = 0;
            foreach ($this->items as $item) {
                $subtotal += (float)$item['price'] * (float)$item['qty'];
            }

            $taxAmount = $subtotal * ((float)$this->tax_rate / 100);
            $grandTotal = $subtotal + $taxAmount;

            $tanggalInput = $this->created_at ?: now()->format('Y-m-d');
            $finalTimestamp = $tanggalInput . ' ' . now()->format('H:i:s');

            $purchase = \App\Models\Purchase::create([
                'supplier_id' => $this->supplier_id,
                'branch_id'   => 1,
                'notes'       => $this->notes,
                'is_tax'      => $this->tax_rate > 0,
                'subtotal'    => $subtotal,
                'tax_amount'  => $taxAmount,
                'tax_rate'    => $this->tax_rate,
                'grand_total' => $grandTotal,
                'status'      => 'Menunggu Approval',
                'created_at'  => $finalTimestamp,
                'due_date'    => $this->due_date ?: $tanggalInput,
            ]);

            foreach ($this->items as $item) {
                $lineSubtotal = (float)$item['price'] * (float)$item['qty'];
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['qty'],
                    'unit_price' => $item['price'],
                    'subtotal'   => $lineSubtotal,
                ]);
            }
        });

        Notification::make()->title('PO Berhasil dibuat!')->success()->send();
        return redirect()->to(PurchaseResource::getUrl('index'));
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->label('Simpan Data ');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()->hidden();
    }
}
