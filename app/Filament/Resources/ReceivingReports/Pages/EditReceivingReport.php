<?php

namespace App\Filament\Resources\ReceivingReports\Pages;

use App\Filament\Resources\ReceivingReports\ReceivingReportResource;
use App\Models\Inventory;
use App\Models\ReceivingItem;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditReceivingReport extends EditRecord
{
    protected static string $resource = ReceivingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol manual jika ingin menutup PO lebih awal meskipun barang belum lengkap
            Action::make('finalize_po')
                ->label('Finalisasi & Terbitkan Tagihan')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Tutup Penerimaan PO')
                ->modalDescription('Tindakan ini akan MENYIMPAN perubahan terbaru, mengunci status PO menjadi Selesai, dan menerbitkan tagihan. Lanjutkan?')
                ->visible(fn() => $this->record->purchase->status === 'Proses Penerimaan')
                ->action(function () {
                    // --- PERBAIKAN DI SINI ---
                    // 1. Simpan data yang sedang diketik di form ke database
                    $this->save();

                    // 2. Karena $this->save() sudah memicu afterSave() secara otomatis, 
                    // stok barang baru sudah bertambah lewat hook afterSave.
                    // Sekarang kita tinggal paksa status PO menjadi final.
                    $this->checkAndFinalizePO($this->record->purchase, true);

                    Notification::make()
                        ->success()
                        ->title('Data Disimpan & PO Ditutup')
                        ->body('Perubahan berhasil disimpan, stok diupdate, dan tagihan telah diterbitkan.')
                        ->send();

                    return redirect($this->getRedirectUrl());
                }),
        ];
    }

    /**
     * Logic ini berjalan setiap kali tombol "Save Changes" diklik.
     * Digunakan untuk menambah stok barang yang BARU ditambahkan di halaman Edit.
     */
    public $oldItemsQty = [];

    protected function beforeSave(): void
    {
        // 1. Ambil snapshot data qty yang ada di DB SAAT INI (sebelum ditimpa form)
        // Kita simpan dalam array dengan format [id_item => qty_received]
        $this->oldItemsQty = \App\Models\ReceivingItem::where('receiving_report_id', $this->record->id)
            ->pluck('qty_received', 'id')
            ->toArray();
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        // 1. Ambil data mentah dari form (array items dengan key UUID)
        $formItems = $this->data['items'] ?? [];

        DB::transaction(function () use ($record, $formItems) {
            $selectedWarehouseId = $record->warehouse_id;

            foreach ($formItems as $uuid => $itemData) {
                // 2. Cari data item berdasarkan purchase_item_id untuk di-update
                $receivingItem = \App\Models\ReceivingItem::where('receiving_report_id', $record->id)
                    ->where('purchase_item_id', $itemData['purchase_item_id'])
                    ->first();

                if ($receivingItem) {
                    // Simpan Qty Lama untuk hitung stok
                    $oldQty = $this->oldItemsQty[$receivingItem->id] ?? 0;
                    $newQty = (float) $itemData['qty_received'];

                    // 3. Update data item di DB
                    $receivingItem->update([
                        'qty_received' => $newQty,
                        'qty_rejected' => (float) $itemData['qty_rejected'],
                        'reject_reason' => $itemData['reject_reason'],
                    ]);

                    // 4. Logic Update Stok (Snapshot beforeSave Anda tetap terpakai)
                    if ($oldQty != $newQty) {
                        $inventory = \App\Models\Inventory::firstOrCreate([
                            'product_id'   => $itemData['product_id'],
                            'warehouse_id' => $selectedWarehouseId,
                            'branch_id'    => 1,
                        ], ['stock' => 0]);

                        if ($oldQty > 0) $inventory->decrement('stock', $oldQty);
                        if ($newQty > 0) $inventory->increment('stock', $newQty);
                    }
                }
            }

            $this->checkAndFinalizePO($record->purchase);
        });
    }

    /**
     * Fungsi Inti: Mengecek kelengkapan PO dan membuat transaksi otomatis.
     */
    protected function checkAndFinalizePO($purchase, bool $forceFinalize = false): void
    {
        if (!$purchase) return;

        $isComplete = true;
        $totalReceivedValue = 0;

        // Refresh data purchase untuk mendapatkan item terbaru yang baru saja di-save
        $purchase->load('items');

        foreach ($purchase->items as $poItem) {
            // Hitung akumulasi qty yang benar-benar diterima dari database
            $totalReceivedQty = ReceivingItem::where('purchase_item_id', $poItem->id)->sum('qty_received');

            // Kalkulasi nilai tagihan berdasarkan harga PO
            $totalReceivedValue += ($totalReceivedQty * $poItem->unit_price);

            if ($totalReceivedQty < $poItem->quantity) {
                $isComplete = false;
            }
        }

        if ($isComplete || $forceFinalize) {
            $taxRate = $purchase->tax_rate;
            $taxAmount = ($taxRate > 0) ? ($totalReceivedValue * ($taxRate / 100)) : 0;
            $grandTotal = $totalReceivedValue + $taxAmount;

            $purchase->update([
                'subtotal' => $totalReceivedValue,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'status' => 'Menunggu Proses Pembayaran',
            ]);

            Transaction::updateOrCreate(
                ['nomor_transaksi' => '' . $purchase->po_number],
                [
                    'tanggal_transaksi' => now(),
                    'kategori' => 'PI',
                    'purchase_id' => $purchase->id,
                    'supplier_id' => $purchase->supplier_id,
                    'user_id' => auth()->id(),
                    'metode_pembayaran' => 'Transfer',
                    'subtotal' => $totalReceivedValue,
                    'pajak' => $taxAmount,
                    'total_akhir' => $grandTotal,
                    'jumlah_terbayar' => 0,
                    'status_bayar' => 'Menunggu Konfirmasi Pembayaran',
                    'keterangan' => 'Tagihan dari PO: ' . $purchase->po_number . ($forceFinalize ? ' (Ditutup Paksa)' : ' (Lengkap)'),
                ]
            );
        } else {
            $purchase->update(['status' => 'Proses Penerimaan']);
        }
    }

    protected function getFormActions(): array
    {
        // Kunci tombol "Save Changes" jika status PO sudah bukan dalam proses penerimaan
        // if ($this->record->purchase->status !== 'Proses Penerimaan') {
        //     return [];
        // }
        return parent::getFormActions();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
