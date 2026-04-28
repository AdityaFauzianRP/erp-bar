@php
    // Cek apakah form terkunci (misal status Approve)
    $isLocked = method_exists($this, 'isLocked') ? $this->isLocked() : false;

    $isRequest = method_exists($this, 'isRequest') ? $this->isRequest() : false;

    // Ambil data kategori untuk dropdown di setiap baris
    $categories = \App\Models\ExpenseCategory::orderBy('name')->get();

    $isCreatePage = $this instanceof \Filament\Resources\Pages\CreateRecord;

    $isHeaderApproved = ($this->record->status ?? '') === 'Approve';

    // 3. Tentukan apakah kolom status harus muncul
    // Kolom HANYA muncul jika: BUKAN halaman Create DAN Header BELUM Approve
    $showStatusColumn = !$isCreatePage && !$isHeaderApproved;

    $showStatusColumn2 = !$isCreatePage && $isHeaderApproved;
@endphp

<div class="custom-item-picker" x-data="{
    openModal: false,
    // Fungsi untuk membuka modal sambil memberi tahu Livewire baris mana yang sedang diedit
    openProductPicker(index, categoryId) {
        if (!categoryId) {
            // Gunakan Toast Modern yang kita buat sebelumnya jika kategori belum dipilih
            showToast('Pilih kategori terlebih dahulu!', 'danger');
            return;
        }
        @this.set('activeRowIndex', index);
        @this.set('selectedCategoryId', categoryId);
        this.openModal = true;
        $nextTick(() => $refs.searchInput.focus());
    }
}" style="font-family: sans-serif;">

    <style>
        .op-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
            margin-top: 10px;
            background: white;
        }

        .op-table th {
            background: #f9fafb;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            border-bottom: 2px solid #e5e7eb;
            color: #374151;
        }

        .op-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .op-input,
        .op-select {
            width: 100%;
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 14px;
        }

        .op-input:disabled,
        .op-select:disabled {
            background-color: #f3f4f6 !important;
            cursor: not-allowed;
            color: #9ca3af;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 50px;
            z-index: 9999;
        }

        .modal-content {
            background: white;
            width: 100%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .modal-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 15px;
            max-height: 400px;
            overflow-y: auto;
        }

        .search-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .product-card {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            transition: background 0.2s;
        }

        .product-card:hover {
            background: #eff6ff;
        }

        .btn-add-row {
            background: #2563eb;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
        }

        .btn-select-item {
            background: #f3f4f6;
            color: #374151;
            padding: 6px 12px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            font-size: 12px;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }
    </style>

    {{-- Tombol Tambah Baris Baru --}}
    @if (!$isHeaderApproved)
        <div style="display: flex; justify-content: flex-start;">
            <button type="button" wire:click="addNewRow" class="btn-add-row">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Item
            </button>
        </div>
    @endif

    <table class="op-table">
        <thead>
            <tr>
                <th style="width: 150px;">Kategori</th>
                <th>Barang / Deskripsi</th>
                <th>Deskripsi Tambahan</th>
                <th style="width: 80px;">Qty</th>
                <th style="width: 150px;">Harga</th>
                <th style="text-align: right; width: 150px;">Subtotal</th>
                @if ($showStatusColumn)
                    <th style="width: 120px; text-align: center;">Status</th>
                @endif

                @if ($showStatusColumn2)
                    <th style="width: 120px; text-align: center;">Status</th>
                @endif

                <th style="width: 40px;"></th>

            </tr>
        </thead>
        <tbody>
            @forelse ($this->items as $index => $item)
                <tr>
                    {{-- Dropdown Kategori --}}
                    <td>
                        <select wire:model.live="items.{{ $index }}.category_id"
                            wire:change="resetItem({{ $index }})" {{-- TAMBAHKAN INI --}} class="op-select"
                            {{ $isLocked ? 'disabled' : '' }}>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </td>

                    {{-- Pemilihan Barang --}}
                    <td>
                        @if ($item['product_id'])
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-weight: 600; color: #111827;">{{ $item['name'] }}</div>
                                    <small style="color: #6b7280;">{{ $item['unit_name'] }}</small>
                                </div>
                                @if (!$isLocked)
                                    <button type="button" wire:click="resetItem({{ $index }})"
                                        style="color: #3b82f6; font-size: 11px;">Ganti</button>
                                @endif
                            </div>
                        @else
                            <button type="button"
                                @click="openProductPicker({{ $index }}, {{ $item['category_id'] ?? 'null' }})"
                                class="btn-select-item" {{ $isLocked ? 'disabled' : '' }}>
                                {{ $item['category_id'] ? '🔍 Klik untuk pilih barang...' : '🔒 Pilih kategori dulu' }}
                            </button>
                        @endif
                    </td>

                    <td>
                        <input type="text" wire:model.live="items.{{ $index }}.description" class="op-input"
                            placeholder="Catatan (opsional)..." {{ $isLocked ? 'disabled' : '' }}>
                    </td>

                    {{-- Qty & Harga --}}
                    <td>
                        <input type="number" step="0.01" wire:model.live="items.{{ $index }}.qty"
                            class="op-input" {{ $isLocked ? 'disabled' : '' }}>
                    </td>
                    <td>
                        <input type="number" wire:model.live="items.{{ $index }}.price" class="op-input"
                            {{ $isLocked ? 'disabled' : '' }}>
                    </td>

                    {{-- Subtotal --}}
                    {{-- Subtotal --}}
                    <td style="text-align: right; font-weight: 600; color: #111827;">
                        @php
                            $qty =
                                filter_var($item['qty'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?: 0;
                            $price =
                                filter_var($item['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?:
                                0;
                            $subtotal = $qty * $price;
                        @endphp
                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                    </td>

                    @if ($showStatusColumn)
                        <td style="text-align: center;">
                            <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                                @php
                                    $status = $item['status'] ?? 'pending';
                                    $statusColor = match ($status) {
                                        'approved' => '#10b981', // Hijau
                                        'rejected' => '#ef4444', // Merah
                                        default => '#f59e0b', // Kuning (Pending)
                                    };
                                @endphp

                                {{-- Label Status --}}
                                <span
                                    style="font-size: 10px; font-weight: bold; text-transform: uppercase; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}; padding: 2px 6px; border-radius: 10px;">
                                    {{ $status }}
                                </span>

                                {{-- Tombol Aksi (Hanya jika belum dilock) --}}
                                @if (!$isLocked)
                                    <div style="display: flex; gap: 5px; margin-top: 5px;">
                                        {{-- Tombol Approve --}}
                                        <button type="button"
                                            wire:click="setItemStatus({{ $index }}, 'approved')"
                                            style="background: #ecfdf5; color: #059669; border: 1px solid #059669; border-radius: 4px; padding: 2px 4px; cursor: pointer;">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>

                                        {{-- Tombol Reject --}}
                                        <button type="button"
                                            wire:click="setItemStatus({{ $index }}, 'rejected')"
                                            style="background: #fef2f2; color: #dc2626; border: 1px solid #dc2626; border-radius: 4px; padding: 2px 4px; cursor: pointer;">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </td>
                    @endif

                    @if ($showStatusColumn2)
                        <td style="text-align: center;">
                            <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                                @php
                                    $status = $item['status'] ?? 'pending';
                                    $statusColor = match ($status) {
                                        'approved' => '#10b981', // Hijau
                                        'rejected' => '#ef4444', // Merah
                                        default => '#f59e0b', // Kuning (Pending)
                                    };
                                @endphp

                                {{-- Label Status --}}
                                <span
                                    style="font-size: 10px; font-weight: bold; text-transform: uppercase; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}; padding: 2px 6px; border-radius: 10px;">
                                    {{ $status }}
                                </span>

                                {{-- Tombol Aksi (Hanya jika belum dilock) --}}
                                @if (!$isLocked)
                                    <div style="display: flex; gap: 5px; margin-top: 5px;">
                                        {{-- Tombol Approve --}}
                                        <button type="button"
                                            wire:click="setItemStatus({{ $index }}, 'approved')"
                                            style="background: #ecfdf5; color: #059669; border: 1px solid #059669; border-radius: 4px; padding: 2px 4px; cursor: pointer;">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>

                                        {{-- Tombol Reject --}}
                                        <button type="button"
                                            wire:click="setItemStatus({{ $index }}, 'rejected')"
                                            style="background: #fef2f2; color: #dc2626; border: 1px solid #dc2626; border-radius: 4px; padding: 2px 4px; cursor: pointer;">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </td>
                    @endif

                    {{-- Hapus Baris --}}
                    <td style="text-align: center;">
                        {{-- Tombol Hapus HANYA muncul jika di halaman Create DAN belum dilock --}}
                        @if ($isCreatePage && !$isLocked)
                            <button type="button" wire:click="removeItem({{ $index }})"
                                style="color: #ef4444; font-size: 20px; cursor: pointer; background: none; border: none;">
                                &times;
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #9ca3af;">Belum ada item. Klik
                        "Tambah Item" untuk memulai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
        <div
            style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; min-width: 300px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="color: #6b7280; font-size: 14px;">Total Item:</span>
                <span style="font-weight: 600; color: #374151;">{{ count($this->items) }}</span>
            </div>
            <div
                style="display: flex; justify-content: space-between; padding-top: 8px; border-top: 2px solid #e5e7eb;">
                <span style="font-size: 16px; font-weight: bold; color: #111827;">Grand Total:</span>
                <span style="font-size: 18px; font-weight: 800; color: #2563eb;">
                    @php
                        $grandTotal = collect($this->items)
                            // Filter: Hanya hitung yang statusnya BUKAN rejected
                            ->filter(function ($item) {
                                return ($item['status'] ?? 'pending') !== 'rejected';
                            })
                            ->sum(function ($item) {
                                $q =
                                    filter_var(
                                        $item['qty'],
                                        FILTER_SANITIZE_NUMBER_FLOAT,
                                        FILTER_FLAG_ALLOW_FRACTION,
                                    ) ?:
                                    0;
                                $p =
                                    filter_var(
                                        $item['price'],
                                        FILTER_SANITIZE_NUMBER_FLOAT,
                                        FILTER_FLAG_ALLOW_FRACTION,
                                    ) ?:
                                    0;
                                return $q * $p;
                            });
                    @endphp
                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Modal Picker --}}
    <div x-show="openModal" class="modal-overlay" x-cloak @keydown.escape.window="openModal = false">
        <div class="modal-content" @click.away="openModal = false">
            <div class="modal-header">
                <div>
                    <h3 style="font-weight: bold; font-size: 16px;">Pilih Produk</h3>
                    <small style="color: #6b7280;">Kategori:
                        {{ \App\Models\ExpenseCategory::find($this->selectedCategoryId)?->name }}</small>
                </div>
                <button type="button" @click="openModal = false"
                    style="border:none; background:none; font-size:28px; cursor:pointer;">&times;</button>
            </div>
            <div class="modal-body">
                <input type="text" wire:model.live="search" x-ref="searchInput" class="search-input"
                    placeholder="Cari nama atau kode barang...">

                <div style="display: flex; flex-direction: column; gap: 5px;">
                    @forelse ($this->products as $p)
                        <div class="product-card" @click="openModal = false"
                            wire:click="selectProduct({{ $p->id }})">
                            <div>
                                <div style="font-weight: 600;">{{ $p->name }}</div>
                                <div style="font-size: 11px; color: #6b7280;">{{ $p->code }}</div>
                            </div>
                            <div style="color: #2563eb; font-weight: bold; align-self: center;">Pilih +</div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 20px; color: #9ca3af;">Barang tidak ditemukan atau
                            kategori salah.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
