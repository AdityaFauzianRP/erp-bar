<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    // Menghubungkan ke Custom Blade (CSS/HTML murni)
    protected string $view = 'filament.resources.transactions.custom-page';

    /**
     * Mengatur lebar halaman menjadi maksimal
     */
    public function getMaxContentWidth(): string
    {
        return 'full';
    }

    /**
     * Logika Otomatisasi Status setelah klik Simpan
     */
    protected function afterSave(): void
    {
        // 1. Refresh data
        $transaction = $this->record->refresh();

        // 2. TULIS LOG UNTUK CEK APAKAH MASUK KE SINI
        \Illuminate\Support\Facades\Log::info('--- DEBUG TRANSAKSI AFTER SAVE ---');
        \Illuminate\Support\Facades\Log::info('ID Transaksi: ' . $transaction->id);
        \Illuminate\Support\Facades\Log::info('Purchase ID: ' . ($transaction->purchase_id ?? 'KOSONG'));

        // 3. Update status Transaksi
        $transaction->update([
            'status_bayar' => 'Terbayar',
            'jumlah_terbayar' => $transaction->total_akhir,
        ]);

        // 4. Update status Purchase
        if ($transaction->purchase_id) {
            $updated = \Illuminate\Support\Facades\DB::table('purchases')
                ->where('id', $transaction->purchase_id)
                ->update(['status' => 'Terbayar']);

            // Log hasil update purchase
            \Illuminate\Support\Facades\Log::info('Status Update Purchase: ' . ($updated ? 'BERHASIL' : 'GAGAL (Mungkin ID tidak ketemu atau status sudah Terbayar)'));
        } else {
            \Illuminate\Support\Facades\Log::info('Update Purchase dilewati karena purchase_id KOSONG');
        }
    }

    /**
     * Redirect kembali ke daftar tabel setelah selesai
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
