<?php

namespace App\Filament\Resources\Purchases\Pages;

use App\Filament\Resources\Purchases\PurchaseResource;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\Warehouse;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class DirectPurchase extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = PurchaseResource::class;
    protected string $view = 'filament.resources.purchases.pages.direct-purchase';
    protected ?string $heading = 'Pembelian Khusus (Direct)';

    // Tambahkan type hint yang lebih fleksibel atau pastikan instance benar
    public $record = null;
    public ?array $data = [];
    public $items = [];
    public $search = '';
    public $subtotal = 0;
    public $grandTotal = 0;
    public $taxAmount = 0;

    public function mount($record = null): void
    {
        // 1. Coba ambil ID dari parameter mount (Route Binding Filament)
        // 2. Jika tidak ada, ambil dari request route
        // 3. Jika tidak ada, ambil dari query string
        $recordId = $record ?? request()->route('record') ?? request()->query('record');

        if ($recordId) {
            // Gunakan where()->first() untuk menjamin hasil adalah Single Model Instance, bukan Collection
            $model = Purchase::with(['items.product', 'supplier', 'transactions'])
                ->where('id', $recordId)
                ->first();

            if ($model) {
                $this->record = $model;

                // Load data header ke form
                $this->data = [
                    'supplier_id' => $this->record->supplier_id,
                    'supplier_name' => $this->record->supplier?->name,
                    'warehouse_id' => $this->record->warehouse_id,
                    'metode_pembayaran' => $this->record->transactions->first()?->metode_pembayaran ?? 'Cash',
                    'tax_rate' => $this->record->tax_rate,
                    'notes' => $this->record->notes,
                    'image_direct' => $this->record->image_direct,
                    'uang_dibawa_direct' => $this->record->uang_dibawa_direct,
                    'uang_kembalian_direct' => $this->record->uang_kembalian_direct,
                ];

                $this->form->fill($this->data);

                // Load data items ke tabel custom
                $this->items = $this->record->items->map(fn($item) => [
                    'product_id' => $item->product_id,
                    'name' => $item->product?->name ?? 'Produk Tidak Ditemukan',
                    'qty' => $item->quantity,
                    'price' => $item->unit_price,
                    'unit_id' => $item->product?->unit_id,
                    'unit_name' => $item->product?->unit?->name ?? '-', // Tambahkan baris ini
                ])->toArray();

                $this->calculateTotal();
            } else {
                // Jika ID ada tapi data tidak ditemukan di DB
                Notification::make()->title('Data ID ' . $recordId . ' tidak ditemukan')->danger()->send();
            }
        } else {
            // Inisialisasi data default untuk Create
            $this->form->fill([
                'tax_rate' => 0,
                'metode_pembayaran' => 'Cash',
                'warehouse_id' => Warehouse::first()?->id,
                'uang_dibawa_direct' => 0, // Default 0
                'uang_kembalian_direct' => 0,
            ]);
        }
    }

    // ... kode bagian atas tetap sama ...

    public function form(Schema $form): Schema
    {

        $isLocked = $this->record && $this->record->status === 'Terbayar';
        return $form


            ->schema([
                ComponentsSection::make('Informasi Transaksi')
                    ->schema([
                        ComponentsGrid::make(3)->schema([
                            Select::make('supplier_id')
                                ->label('Supplier')
                                ->options(Supplier::pluck('name', 'id'))
                                ->searchable()->preload()->required()->columnSpan(3)->disabled($isLocked),

                            Select::make('warehouse_id')
                                ->label('Gudang Tujuan')
                                ->options(Warehouse::pluck('name', 'id'))
                                ->required()->disabled($isLocked),

                            Select::make('metode_pembayaran')
                                ->label('Metode Bayar')
                                ->options(['Cash' => 'Cash', 'Transfer' => 'Transfer'])
                                ->required()->disabled($isLocked),

                            TextInput::make('tax_rate')
                                ->label('PPN (%)')
                                ->numeric()->live()
                                ->afterStateUpdated(fn() => $this->calculateTotal()),
                        ]),
                    ])->compact(),

                // SECTION BARU: PEMBAYARAN & BUKTI
                ComponentsSection::make('Pembayaran & Bukti')
                    ->schema([
                        ComponentsGrid::make(2)->schema([
                            TextInput::make('uang_dibawa_direct')
                                ->label('Uang Dibawa')
                                ->numeric()
                                ->prefix('Rp')
                                ->live()
                                ->disabled($isLocked),

                            TextInput::make('uang_kembalian_direct')
                                ->label('Uang Kembalian')
                                ->numeric()
                                ->prefix('Rp')
                                ->live()
                                ->disabled($isLocked),

                            FileUpload::make('image_direct')
                                ->label('Upload Bukti Nota/Barang')
                                ->image()
                                ->multiple()
                                ->directory('purchase-direct')
                                ->visibility('public') // <--- TAMBAHKAN INI agar file tidak "Forbidden"
                                ->disk('public')       // <--- TAMBAHKAN INI agar masuk ke storage/app/public
                                ->columnSpanFull()
                                ->disabled($isLocked),
                        ])
                    ])->compact(),

                ComponentsSection::make('Tambahan')
                    ->schema([
                        TextInput::make('notes')->label('Catatan')->disabled($isLocked),
                    ])->compact()
            ])
            ->statePath('data');
    }

    public function approve()
    {
        if (!$this->record) return;

        \Illuminate\Support\Facades\DB::transaction(function () {
            // 1. Update status Purchase
            $this->record->update([
                'status'      => 'Terbayar',
                'approved_by' => auth()->id(),
            ]);

            // 2. Tambah stok di tabel inventories
            foreach ($this->record->items as $item) {
                \App\Models\Inventory::where('warehouse_id', $this->record->warehouse_id)
                    ->where('product_id', $item->product_id)
                    ->increment('stock', $item->quantity);
            }

            // 3. Sinkronkan status transaksi
            \App\Models\Transaction::where('purchase_id', $this->record->id)
                ->update(['status_bayar' => 'Terbayar']);
        });

        \Filament\Notifications\Notification::make()
            ->title('Berhasil Approve & Update Stok')
            ->success()
            ->send();

        return redirect(request()->header('Referer'));
    }

        public function calculateTotal()
    {
        // Gunakan array_map atau collection tanpa mengubah state $this->items di dalam loop
        $this->subtotal = collect($this->items)->reduce(function ($carry, $item) {
            // Membersihkan karakter non-numeric kecuali titik/koma desimal
            $qty = (float) filter_var($item['qty'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $price = (float) filter_var($item['price'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

            return $carry + ($qty * $price);
        }, 0);

        $taxRate = (float)($this->data['tax_rate'] ?? 0);
        $this->taxAmount = ($this->subtotal * $taxRate) / 100;
        $this->grandTotal = $this->subtotal + $this->taxAmount;
    }

    public function addItem($productId)
    {
        // Load produk beserta relasi unit-nya
        $product = Product::with('unit')->find($productId);

        if (!$product) return;

        $this->items[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'qty' => 1,
            'price' => $product->hpp ?? 0,
            'unit_id' => $product->unit_id, // Simpan ID unit
            'unit_name' => $product->unit?->name ?? '-', // Simpan Nama unit untuk tampilan
        ];

        $this->search = '';
        $this->calculateTotal();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotal();
    }

    public function save()
    {
        $headerData = $this->form->getState();

        if (empty($this->items)) {
            Notification::make()->title('Harap pilih minimal 1 produk')->danger()->send();
            return;
        }

        DB::transaction(function () use ($headerData) {
            // Gunakan $this->record jika sedang edit, jika tidak buat baru
            $purchase = $this->record ?? new Purchase();

            $purchase->fill([
                'po_number' => $purchase->po_number ?? 'PO-' . now()->format('YmdHis'),
                'supplier_id' => $headerData['supplier_id'],
                'warehouse_id' => $headerData['warehouse_id'],
                'tax_rate' => $headerData['tax_rate'],
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->taxAmount,
                'grand_total' => $this->grandTotal,
                'status' => $purchase->status ?? 'Menunggu Approval',
                'notes' => $headerData['notes'],
                'branch_id' => auth()->user()->branch_id ?? 1,
                'created_by' => auth()->id(),
                'due_date' => now(),
                'image_direct' => $headerData['image_direct'] ?? [],
                'uang_dibawa_direct' => $headerData['uang_dibawa_direct'] ?? 0,
                'uang_kembalian_direct' => $headerData['uang_kembalian_direct'] ?? 0,
            ]);
            $purchase->save();

            // Simpan Items (Hapus yang lama lalu masukkan yang baru untuk edit)
            $purchase->items()->delete();
            // Di dalam loop foreach ($this->items as $item) pada fungsi save()
            foreach ($this->items as $item) {
                $qty = (float) str_replace(',', '.', $item['qty']);
                $price = (float) str_replace(',', '.', $item['price']);

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $item['product_id'],
                    'quantity'    => $qty,
                    'unit_price'  => $price,
                    'subtotal'    => $qty * $price, // Sekarang aman karena keduanya float
                ]);
            }

            // Update/Create Transaction record
            Transaction::updateOrCreate(
                ['purchase_id' => $purchase->id],
                [
                    'nomor_transaksi' => 'INV-' . $purchase->po_number,
                    'tanggal_transaksi' => now(),
                    'kategori' => 'PO-DIRECT',
                    'metode_pembayaran' => $headerData['metode_pembayaran'],
                    'subtotal' => $this->subtotal,
                    'total_akhir' => $this->grandTotal,
                    'pajak' => $this->taxAmount,
                    'status_bayar' => $purchase->status,
                    'supplier_id' => $headerData['supplier_id'],
                    'user_id' => auth()->id(),
                ]
            );
        });

        Notification::make()->title('Transaksi Berhasil Disimpan')->success()->send();
        return redirect()->to(PurchaseResource::getUrl('index'));
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label($this->record ? 'Simpan Perubahan' : 'Proses Pembelian')
                ->color('primary')
                ->submit('save') // Ini akan memicu method save()
                ->disabled(fn() => $this->record && $this->record->status === 'Terbayar')
                // Filament otomatis handle loading & disabled saat upload!
                ->keyBindings(['mod+s']),

            \Filament\Actions\Action::make('approve')
                ->label('Setujui Pembelian')
                ->color('success')
                ->action('approve')
                ->requiresConfirmation()
                ->visible(function () {
                    // Syarat 1: Record harus ada
                    // Syarat 2: Status belum 'Terbayar'
                    // Syarat 3: User harus punya permission 'ApprovePurchase'
                    return $this->record &&
                        $this->record->status !== 'Terbayar' &&
                        auth()->user()->can('ApprovePurchase'); // Cek permission di sini
                }),
        ];
    }
}
