<?php

namespace App\Filament\Resources\ReceivingReports\Pages;

use App\Filament\Resources\ReceivingReports\ReceivingReportResource;
use App\Models\ReceivingReport;
use App\Models\Purchase;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\ReceivingItem;
use App\Models\Transaction;
use App\Models\Product;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ReceiveGoods extends Page
{
    protected static string $resource = ReceivingReportResource::class;

    protected string $view = 'filament.resources.receiving-reports.pages.receive-goods';

    public $record = null;
    public $purchase_id, $warehouse_id, $receive_number, $notes;
    public $items = [];
    public $delivery_notes = [''];

    public function addDeliveryNote()
    {
        $this->delivery_notes[] = '';
    }

    public function removeDeliveryNote($index)
    {
        unset($this->delivery_notes[$index]);
        $this->delivery_notes = array_values($this->delivery_notes);
    }

    public function mount($record = null): void
    {
        if ($record) {
            $model = ReceivingReport::with(['items.purchaseItem.product', 'purchase'])->find($record);

            if (! $model) {
                abort(404, 'Data penerimaan tidak ditemukan.');
            }

            $this->record = $model;
            $this->purchase_id = $model->purchase_id;
            $this->warehouse_id = $model->warehouse_id;
            $this->receive_number = $model->receive_number;
            $this->delivery_notes = json_decode($model->delivery_note_number, true) ?: [''];
            $this->notes = $model->notes;

            $this->items = $model->items->map(fn($item) => [
                'id' => $item->id,
                'purchase_item_id' => $item->purchase_item_id,
                'product_id' => $item->product_id,
                'product_name' => $item->purchaseItem->product->name ?? 'Produk N/A',
                'qty_po' => $item->purchaseItem->quantity ?? 0,
                'qty_received' => $item->qty_received,
                'qty_rejected' => $item->qty_rejected,
                'reject_reason' => $item->reject_reason,
            ])->toArray();
        } else {
            $this->receive_number = 'GR-' . date('Ymd') . '-' . strtoupper(str()->random(4));
        }
    }

    public function updatedPurchaseId($value)
    {
        if (!$value) {
            $this->items = [];
            return;
        }

        $purchase = Purchase::with('items.product')->find($value);
        if ($purchase) {
            $this->items = $purchase->items->map(fn($item) => [
                'purchase_item_id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'qty_po' => $item->quantity,
                'qty_received' => $item->quantity,
                'qty_rejected' => 0,
                'reject_reason' => '',
            ])->toArray();
        }
    }

    public function save()
    {
        $this->validate([
            'purchase_id' => 'required',
            'warehouse_id' => 'required',
            'items.*.qty_received' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () {
            // 1. Rollback stok lama jika dalam mode EDIT
            if ($this->record) {
                foreach ($this->record->items as $oldItem) {
                    $this->updateStock($oldItem->product_id, -$oldItem->qty_received);
                }
            }

            $dataHeader = [
                'purchase_id' => $this->purchase_id,
                'warehouse_id' => $this->warehouse_id,
                'receive_number' => $this->receive_number,
                'delivery_note_number' => json_encode(array_filter($this->delivery_notes)),
                'notes' => $this->notes,
                'received_by' => auth()->id(),
                'received_date' => now(),
            ];

            if ($this->record) {
                $this->record->update($dataHeader);
                $this->record->items()->delete();
                $report = $this->record;
            } else {
                $report = ReceivingReport::create($dataHeader);
                $this->record = $report;
            }

            // 2. Simpan Item Baru & Tambah Stok Baru (Hanya Qty Bagus)
            foreach ($this->items as $itemData) {
                $newItem = $report->items()->create($itemData);
                
                if ($newItem->qty_received > 0) {
                    $this->updateStock($newItem->product_id, $newItem->qty_received);
                }
            }
        });

        Notification::make()->success()->title('Data Berhasil Disimpan & Stok Diperbarui')->send();
        return redirect(static::$resource::getUrl('index'));
    }

    public function finalize()
    {
        // Jalankan Save dulu untuk memastikan data terbaru masuk DB dan stok ter-update
        $this->save();

        try {
            DB::transaction(function () {
                $receiving = $this->record;
                $purchase = Purchase::with('items')->find($this->purchase_id);

                // LOGIC: Hitung Nilai Transaksi hanya dari barang yang DITERIMA
                $totalReceivedValue = 0;
                foreach ($purchase->items as $poItem) {
                    // Cari total akumulasi qty bagus yang sudah diterima untuk baris PO ini
                    $totalQtyBagus = ReceivingItem::where('purchase_item_id', $poItem->id)->sum('qty_received');
                    $totalReceivedValue += ($totalQtyBagus * ($poItem->unit_price ?? 0));
                }

                $taxRate = $purchase->tax_rate ?? 0;
                $taxAmount = ($taxRate > 0) ? ($totalReceivedValue * ($taxRate / 100)) : 0;
                $grandTotal = $totalReceivedValue + $taxAmount;

                // Buat Invoice (PI) otomatis
                $transaction = \App\Models\Transaction::create([
                    'nomor_transaksi'   => 'INV-' . $purchase->po_number,
                    'purchase_id'       => $purchase->id,
                    'supplier_id'       => $purchase->supplier_id,
                    'user_id'           => auth()->id(),
                    'tanggal_transaksi' => now(),
                    'kategori'          => 'PI',
                    'status_bayar'      => 'Belum Lunas',
                    'subtotal'          => $totalReceivedValue,
                    'pajak'             => $taxAmount,
                    'total_akhir'       => $grandTotal,
                    'jumlah_terbayar'   => 0,
                    'keterangan'        => 'Tagihan otomatis berdasarkan penerimaan barang dari PO: ' . $purchase->po_number,
                ]);

                $receiving->update(['status' => 'Completed']);
                $purchase->update(['status' => 'Received']);
            });

            Notification::make()->success()->title('Penerimaan Selesai & Tagihan Diterbitkan')->send();
            return redirect(static::$resource::getUrl('index'));

        } catch (\Exception $e) {
            Notification::make()->danger()->title('Gagal Finalisasi')->body($e->getMessage())->send();
        }
    }

    /**
     * Helper untuk Update Stok ke Tabel Inventory
     */
    protected function updateStock($productId, $qty)
    {
        if ($qty == 0) return;

        $inventory = Inventory::firstOrCreate(
            [
                'product_id'   => $productId,
                'warehouse_id' => $this->warehouse_id,
                'branch_id'    => 1, // Sesuaikan branch ID jika dinamis
            ],
            ['stock' => 0]
        );

        $inventory->increment('stock', $qty);
    }
}