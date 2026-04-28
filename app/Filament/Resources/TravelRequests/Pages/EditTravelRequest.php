<?php

namespace App\Filament\Resources\TravelRequests\Pages;

use App\Filament\Resources\OperationalExpenses\Schemas\OperationalExpenseForm;
use App\Filament\Resources\TravelRequests\TravelRequestResource;
use App\Models\OperationalExpenseItem;
use App\Models\OperationalProduct;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditTravelRequest extends EditRecord
{
    protected static string $resource = TravelRequestResource::class;

    public $search = '';
    public $items = [];

    public function addItem($productId)
    {
        $product = OperationalProduct::with('unit')->find($productId);
        if (!$product) return;

        $this->items[] = [
            'product_id' => $product->id,
            'name'       => $product->name,
            'unit_name'  => $product->unit?->name ?? '-',
            'qty'        => 1,
            'price'      => 0,
            'subtotal'   => 0,
        ];

        $this->search = '';
    }

    protected function getFormActions(): array
    {
        return [
            // Tombol Save bawaan
            parent::getSaveFormAction(),

            \Filament\Actions\Action::make('approve')
                ->label('Approve Pengajuan')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                // Hanya muncul jika punya izin dan dokumen belum di-approve
                ->visible(fn() => auth()->user()->can('ApprovePerjalananDinas') && $this->record->status !== 'Approve')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Persetujuan Final')
                ->modalDescription('Menyetujui pengajuan ini akan mengunci data dan memperbarui nominal berdasarkan item yang disetujui (Approved).')
                ->action(function () {
                    DB::transaction(function () {
                        // 1. Hitung ulang total hanya dari item yang statusnya 'approved'
                        // Ini penting agar angka di database sesuai dengan item yang dicentang hijau
                        $totalApproved = collect($this->items)
                            ->filter(fn($item) => ($item['status'] ?? 'pending') === 'approved')
                            ->sum(fn($item) => ($item['qty'] ?? 0) * ($item['price'] ?? 0));

                        // 2. Generate Nomor Baru (PERDIN/...)

                        // 3. Update Header (Record Utama)
                        $this->record->update([
                            'status'        => 'Approve',
                            'total_amount'  => $totalApproved, // Simpan total yang sudah difilter
                            'approved_at'   => now(),
                            'approved_by'   => auth()->id(),
                        ]);

                        // 4. Sinkronisasi Item Detail
                        // Kita hapus yang lama dan masukkan kondisi terbaru dari variabel $this->items
                        \App\Models\OperationalExpenseItem::where('operational_expense_id', $this->record->id)->delete();

                        foreach ($this->items as $item) {
                            if (empty($item['product_id'])) continue;

                            \App\Models\OperationalExpenseItem::create([
                                'operational_expense_id' => $this->record->id,
                                'category_id'            => $item['category_id'],
                                'operational_product_id' => $item['product_id'],
                                'qty'                    => $item['qty'],
                                'price'                  => $item['price'],
                                'description'            => $item['description'],
                                'subtotal'               => $item['qty'] * $item['price'],
                                'status'                 => $item['status'] ?? 'pending',
                            ]);
                        }
                    });

                    Notification::make()
                        ->title('Pengajuan Berhasil Disetujui')
                        ->body('Data telah di-approve dan dikunci. Data pengajuan telah diperbarui.')
                        ->success()
                        ->send();

                    return redirect($this->getResource()::getUrl('index'));
                }),

            // Tombol Cancel bawaan
            parent::getCancelFormAction(),
        ];
    }


    public $selectedCategoryId = null;
    public $activeRowIndex = null;

    /**
     * Mengecek apakah data dikunci (Approved & PERDIN)
     */
    public function isLocked(): bool
    {
        return $this->record->status === 'Approve';
    }

    /**
     * Lifecycle Hook: Mengisi properti $items saat halaman Edit dibuka.
     */
    protected function fillForm(): void
    {
        parent::fillForm();

        // Ambil data detail termasuk category_id
        $this->items = OperationalExpenseItem::where('operational_expense_id', $this->record->id)
            ->get()
            ->map(function ($item) {
                return [
                    'id'          => $item->id,
                    'category_id' => $item->category_id, // Tambahkan category_id dari DB
                    'product_id'  => $item->operational_product_id,
                    'name'        => $item->product?->name,
                    'unit_name'   => $item->product?->unit?->name ?? '-',
                    'qty'         => $item->qty,
                    'price'       => $item->price,
                    'description' => $item->description, // Tambahkan description dari DB
                    'subtotal'    => $item->qty * $item->price,
                    'status'      => $item->status ?? 'pending', // Tandai sebagai data existing
                ];
            })->toArray();
    }

    /**
     * Tambah Baris Kosong
     */
    public function addNewRow()
    {
        if ($this->isLocked()) return;

        $this->items[] = [
            'category_id' => null,
            'product_id'  => null,
            'name'        => '',
            'unit_name'   => '',
            'description' => '',
            'qty'         => 1,
            'price'       => 0,
            'status'      => 'pending',
        ];
    }

    /**
     * Reset pilihan barang dalam baris
     */
    public function resetItem($index)
    {
        if ($this->isLocked()) return;

        $this->items[$index]['product_id'] = null;
        $this->items[$index]['name'] = '';
        $this->items[$index]['unit_name'] = '';
    }

    /**
     * Computed Property: Ambil produk berdasarkan kategori baris aktif
     */
    public function getProductsProperty()
    {
        if (!$this->selectedCategoryId) {
            return collect();
        }

        $query = OperationalProduct::where('is_active', true)
            ->where('category_id', $this->selectedCategoryId);

        if (strlen($this->search) > 0) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        return $query->limit(15)->get();
    }

    /**
     * Pilih Produk dari Modal
     */
    public function selectProduct($productId)
    {
        if ($this->isLocked()) return;

        $product = OperationalProduct::with('unit')->find($productId);

        if ($product && $this->activeRowIndex !== null) {
            $this->items[$this->activeRowIndex]['product_id'] = $product->id;
            $this->items[$this->activeRowIndex]['name']       = $product->name;
            $this->items[$this->activeRowIndex]['unit_name']  = $product->unit->name ?? '-';
        }

        // Reset state modal
        $this->activeRowIndex = null;
        $this->selectedCategoryId = null;
        $this->search = '';
    }

    public function removeItem($index)
    {
        if ($this->isLocked()) return;

        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    /**
     * Simpan Perubahan
     */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // if ($this->isLocked()) {
        //     Notification::make()->title('Gagal Update')->danger()->send();
        //     return $record;
        // }

        return DB::transaction(function () use ($record, $data) {
            // --- PERBAIKAN DI SINI: Filter hanya yang statusnya 'approved' ---
            $total = collect($this->items)
                ->filter(fn($item) => ($item['status'] ?? 'pending') === 'approved')
                ->sum(fn($item) => $item['qty'] * $item['price']);

            $data['total_amount'] = $total;

            $record->update($data);

            foreach ($this->items as $item) {
                if (empty($item['product_id'])) continue;

                // Jika ada ID, berarti ini update data lama
                if (isset($item['id'])) {
                    OperationalExpenseItem::where('id', $item['id'])->update([
                        'category_id'            => $item['category_id'],
                        'operational_product_id' => $item['product_id'],
                        'qty'                    => $item['qty'],
                        'price'                  => $item['price'],
                        'description'            => $item['description'],
                        'subtotal'               => $item['qty'] * $item['price'],
                    ]);
                } else {
                    // Jika tidak ada ID, berarti ini item baru yang ditambah saat Edit
                    OperationalExpenseItem::create([
                        'operational_expense_id' => $record->id,
                        'category_id'            => $item['category_id'],
                        'operational_product_id' => $item['product_id'],
                        'qty'                    => $item['qty'],
                        'price'                  => $item['price'],
                        'description'            => $item['description'],
                        'subtotal'               => $item['qty'] * $item['price'],
                        'status'                 => 'Pending', // Default untuk item baru
                    ]);
                }
            }

            return $record;
        });
    }

    public function setItemStatus($index, $status)
    {

        // Update status di array temporary $items
        $this->items[$index]['status'] = $status;

        // Notifikasi opsional agar user tahu tombol berhasil diklik
        Notification::make()
            ->title('Status item diperbarui ke ' . $status)
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
