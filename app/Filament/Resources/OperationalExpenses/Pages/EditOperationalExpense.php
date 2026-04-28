<?php

namespace App\Filament\Resources\OperationalExpenses\Pages;

use App\Filament\Resources\OperationalExpenses\OperationalExpenseResource;
use App\Models\OperationalExpenseItem;
use App\Models\OperationalProduct;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditOperationalExpense extends EditRecord
{
    protected static string $resource = OperationalExpenseResource::class;

    // Properti Publik agar sinkron dengan Blade
    public $search = '';
    public $items = [];
    public $selectedCategoryId = null;
    public $activeRowIndex = null;

    /**
     * Mengecek apakah data dikunci (Approved & PERDIN)
     */
    public function isLocked(): bool
    {
        return $this->record->status === 'operational';
    }

    /**
     * Lifecycle Hook: Mengisi properti $items saat halaman Edit dibuka.
     */
    protected function fillForm(): void
    {
        parent::fillForm();

        $this->items = OperationalExpenseItem::where('operational_expense_id', $this->record->id)
            ->get()
            // Filter: Hanya ambil yang statusnya BUKAN rejected
            ->filter(fn($item) => strtolower($item->status) !== 'rejected')
            ->map(function ($item) {
                return [
                    'id'          => $item->id,
                    'category_id' => $item->category_id,
                    'product_id'  => $item->operational_product_id,
                    'name'        => $item->product?->name,
                    'unit_name'   => $item->product?->unit?->name ?? '-',
                    'qty'         => $item->qty,
                    'price'       => $item->price,
                    'description' => $item->description,
                    'status'      => $item->status ?? 'pending',
                    'subtotal'    => $item->qty * $item->price,
                ];
            })
            ->values() // Reset index array agar tetap berurutan (0, 1, 2...)
            ->toArray();
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
            'qty'         => 1,
            'description' => '',
            'price'       => 0,

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
        if ($this->isLocked()) {
            \Filament\Notifications\Notification::make()
                ->title('Gagal Update')
                ->body('Dokumen yang sudah Approve tidak boleh diedit.')
                ->danger()
                ->send();

            return $record;
        }

        return DB::transaction(function () use ($record, $data) {
            // 1. Hitung Grand Total baru dari state items
            $total = collect($this->items)
                ->filter(function ($item) {
                    // Kita pastikan hanya yang statusnya 'approved' yang dihitung
                    return isset($item['status']) && $status = strtolower($item['status']) === 'approved';
                })
                ->sum(fn($item) => $item['qty'] * $item['price']);

            // Masukkan hasil hitung ke data yang akan diupdate ke record header
            $data['total_amount'] = $total;

            // 2. Update Header (OperationalExpense)
            $record->update($data);

            // 3. Update Detail secara Individual
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
                        // Status tidak diupdate di sini, jadi tetap aman
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        if ($this->isLocked()) {
            return [];
        }

        return parent::getFormActions();
    }
}
