@php
    $isLocked = $record && $record->status === 'Terbayar';

    $existingIds = collect($items)->pluck('product_id')->toArray();
    $availableProducts = \App\Models\Product::whereNotIn('id', $existingIds)
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
        ->limit(10)
        ->get();
@endphp

<x-filament-panels::page>
    <form wire:submit.prevent="save">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">

            {{-- SISI KIRI: FORM & TABEL --}}
            <div>
                <div
                    style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
                    {{ $this->form }}
                </div>

                <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div
                        style="padding: 15px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
                        <span style="font-weight: bold; color: #475569;">Daftar Item Barang</span>
                        @if (!$isLocked)
                            <div x-data="{}"
                                x-on:keydown.window.ctrl.b.prevent="$dispatch('open-modal', { id: 'modal-produk' })">
                                <button type="button" x-on:click="$dispatch('open-modal', { id: 'modal-produk' })"
                                    style="background: #10b981; color: white; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 13px; border: none; cursor: pointer;">
                                    + Tambah Produk (Ctrl + B)
                                </button>
                            </div>
                        @endif
                    </div>

                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #f1f5f9;">
                            <tr>
                                <th style="text-align: left; padding: 12px; font-size: 11px; color: #64748b;">Produk
                                </th>
                                <th style="width: 100px; padding: 12px; font-size: 11px; color: #64748b;">Qty</th>
                                <th style="width: 150px; padding: 12px; font-size: 11px; color: #64748b;">Harga Satuan
                                </th>
                                <th style="width: 150px; padding: 12px; font-size: 11px; color: #64748b;">Subtotal</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $index => $item)
                                <tr wire:key="item-row-{{ $item['product_id'] }}-{{ $index }}"
                                    style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px;"><strong>{{ $item['name'] }}</strong></td>

                                    {{-- Input QTY --}}
                                    <td style="padding: 12px;">
                                        <input type="number" step="any" {{-- Gunakan .blur atau .live.debounce.500ms agar tidak lag --}}
                                            wire:model.blur="items.{{ $index }}.qty" wire:change="calculateTotal"
                                            {{ $isLocked ? 'disabled' : '' }}
                                            style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px;">
                                    </td>

                                    {{-- Input Harga --}}
                                    <td style="padding: 12px;">
                                        <input type="number" wire:model.blur="items.{{ $index }}.price"
                                            wire:change="calculateTotal" {{ $isLocked ? 'disabled' : '' }}
                                            style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px;">
                                    </td>

                                    <td style="padding: 12px;">
                                        Rp
                                        {{ number_format((float) $item['qty'] * (float) $item['price'], 2, ',', '.') }}
                                    </td>

                                    <td style="padding: 12px; text-align: center;">
                                        @if (!$isLocked)
                                            <button type="button" wire:click="removeItem({{ $index }})"
                                                style="color: #ef4444; background: transparent; border: none; cursor: pointer;">
                                                <x-heroicon-m-trash style="width: 20px;" />
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 40px; text-align: center; color: #94a3b8;">Belum
                                        ada produk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- SISI KANAN: RINGKASAN --}}
            <div>
                <div
                    style="background: white; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; border-top: 4px solid #2563eb; position: sticky; top: 20px;">
                    <h3 style="font-weight: bold; font-size: 18px; margin-bottom: 20px;">Ringkasan Biaya</h3>

                    {{-- BARIS SUBTOTAL --}}
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    {{-- BARIS PPN (Ditambahkan) --}}
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #64748b; font-size: 14px;">
                        <span>PPN ({{ $data['tax_rate'] ?? 0 }}%)</span>
                        <span>Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                    </div>

                    {{-- BARIS TOTAL AKHIR --}}
                    <div
                        style="display: flex; justify-content: space-between; margin-top: 15px; margin-bottom: 25px; font-weight: bold; border-top: 1px dashed #e2e8f0; padding-top: 15px;">
                        <span>TOTAL AKHIR</span>
                        <span style="color: #2563eb; font-size: 20px;">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </span>
                    </div>

                    <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #f1f5f9;">

                    {{-- BUTTON ACTIONS --}}
                    <div class="flex flex-col gap-3">
                        <x-filament::actions :actions="$this->getFormActions()" layout="v-stack" class="w-full" />
                    </div>

                    {{-- Status Upload --}}
                    <div wire:loading wire:target="data.image_direct"
                        class="mt-4 text-center text-xs text-danger-600 font-bold animate-pulse">
                        ⏳ SEDANG MENGUPLOAD...
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- MODAL PRODUK --}}
    <x-filament::modal id="modal-produk" width="2xl">
        <x-slot name="header">Tambah Produk</x-slot>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..."
            style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; margin-bottom: 10px;">
        @foreach ($availableProducts as $product)
            <div wire:click="addItem({{ $product->id }})" x-on:click="close()"
                style="padding: 12px; border-bottom: 1px solid #f1f5f9; cursor: pointer;">
                <strong>{{ $product->name }}</strong>
            </div>
        @endforeach
    </x-filament::modal>
</x-filament-panels::page>
