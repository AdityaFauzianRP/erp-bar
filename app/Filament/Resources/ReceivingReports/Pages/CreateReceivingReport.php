<?php

namespace App\Filament\Resources\ReceivingReports\Pages;

use App\Filament\Resources\ReceivingReports\ReceivingReportResource;
use App\Models\Inventory;
use App\Models\ReceivingItem;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateReceivingReport extends CreateRecord
{
    protected static string $resource = ReceivingReportResource::class;

    /**
     * Menangani pembuatan record secara manual untuk menangkap array items ber-UUID
     */

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan Data Penerimaan');
    }

    // 2. Menghilangkan Tombol "Create & Create Another"
    protected function getCreateAnotherFormAction(): Action
    {
        // Kita buat action kosong (empty) agar tidak muncul di view
        return parent::getCreateAnotherFormAction()
            ->hidden();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_and_finalize')
                ->label('Simpan & Selesaikan PO')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    // 1. Jalankan proses create standar (ini akan memicu handleRecordCreation & afterCreate)
                    $this->create();

                    // 2. Setelah $this->create() selesai, $this->record sudah berisi data ReceivingReport yang baru
                    $record = $this->record;

                    if ($record && $record->purchase) {
                        // 3. Paksa penutupan PO
                        $this->checkAndFinalizePO($record->purchase, true);

                        Notification::make()
                            ->success()
                            ->title('PO Berhasil Ditutup')
                            ->body('Penerimaan disimpan dan tagihan telah diterbitkan.')
                            ->send();

                        return redirect($this->getRedirectUrl());
                    }
                }),
        ];
    }


    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {
            // 1. Ambil data mentah dari form (array items dengan key UUID)
            $itemsData = $data['items'] ?? [];

            // 2. Hapus 'items' dari $data utama agar tidak error saat create report (header)
            unset($data['items']);

            // 3. Simpan Header (receiving_reports)
            $record = static::getModel()::create($data);

            // 4. Looping data mentah yang tadi ditangkap
            foreach ($itemsData as $uuid => $item) {
                // Pastikan hanya menyimpan jika ada product_id dan qty
                if (!empty($item['product_id']) && isset($item['qty_received'])) {
                    $record->items()->create([
                        'purchase_item_id' => $item['purchase_item_id'] ?? null,
                        'product_id'       => $item['product_id'],
                        'qty_received'     => (float) $item['qty_received'],
                        'qty_rejected'     => (float) ($item['qty_rejected'] ?? 0),
                        'reject_reason'    => $item['reject_reason'] ?? null,
                    ]);
                }
            }

            return $record;
        });
    }

    /**
     * Eksekusi setelah record dan items berhasil dibuat di handleRecordCreation
     */
    protected function afterCreate(): void
    {
        $record = $this->record;

        DB::transaction(function () use ($record) {
            foreach ($record->items as $item) {
                // Update Stok di Gudang
                $inventory = Inventory::firstOrCreate(
                    [
                        'product_id'   => $item->product_id,
                        'warehouse_id' => $record->warehouse_id,
                        'branch_id'    => 1,
                    ],
                    ['stock' => 0]
                );

                if ($item->qty_received > 0) {
                    $inventory->increment('stock', $item->qty_received);
                }
            }

            // Jalankan pengecekan PO otomatis
            $this->checkAndFinalizePO($record->purchase);
        });
    }

    protected function checkAndFinalizePO($purchase, bool $forceFinalize = false): void
    {
        if (!$purchase) return;

        $purchase->load('items');
        $isComplete = true;
        $totalReceivedValue = 0;

        foreach ($purchase->items as $poItem) {
            $totalReceivedQty = ReceivingItem::where('purchase_item_id', $poItem->id)->sum('qty_received');
            $totalReceivedValue += ($totalReceivedQty * $poItem->unit_price);

            if ($totalReceivedQty < $poItem->quantity) {
                $isComplete = false;
            }
        }

        if ($isComplete || $forceFinalize) {
            $taxRate = $purchase->tax_rate ?? 0;
            $taxAmount = ($taxRate > 0) ? ($totalReceivedValue * ($taxRate / 100)) : 0;
            $grandTotal = $totalReceivedValue + $taxAmount;

            $purchase->update(['status' => 'Menunggu Proses Pembayaran']);

            Transaction::updateOrCreate(
                ['nomor_transaksi' => 'INV-' . $purchase->po_number],
                [
                    'tanggal_transaksi' => now(),
                    'kategori' => 'PO',
                    'purchase_id' => $purchase->id,
                    'supplier_id' => $purchase->supplier_id,
                    'user_id' => auth()->id(),
                    'subtotal' => $totalReceivedValue,
                    'pajak' => $taxAmount,
                    'total_akhir' => $grandTotal,
                    'status_bayar' => 'Menunggu Konfirmasi Pembayaran',
                    'metode_pembayaran' => 'Cash',
                ]
            );
        } else {
            $purchase->update(['status' => 'Proses Penerimaan']);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    
    

    // 2. Menghilangkan Tombol "Create & Create Another"
    
}
