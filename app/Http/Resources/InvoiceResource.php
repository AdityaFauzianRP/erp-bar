<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'status' => $this->status,
            'payment_info' => [
                'method' => $this->payment_method,
                'paid_at' => $this->paid_at ? $this->paid_at->format('d M Y H:i') : null,
                'proof_url' => $this->payment_proof ? asset('storage/' . $this->payment_proof) : null,
            ],
            'dates' => [
                'invoice_date' => $this->invoice_date->format('Y-m-d'),
                'due_date' => $this->due_date->format('Y-m-d'),
                'is_overdue' => $this->status === 'unpaid' && $this->due_date->isPast(),
            ],
            'customer' => [
                'name' => $this->proformaInvoice->customerInduk->name ?? 'N/A',
                'address' => $this->proformaInvoice->customerInduk->address ?? '',
            ],
            'financials' => [
                'subtotal' => (float) $this->subtotal,
                'tax_amount' => (float) $this->tax_amount,
                'total_amount' => (float) $this->total_amount,
            ],
            // Mengambil item dari Delivery karena tagihan sesuai barang yang sampai
            'items' => $this->delivery->items->map(function ($item) {
                $unitPrice = $item->proformaInvoiceItem->unit_price ?? 0;
                return [
                    'product_name' => $item->product->name ?? 'Unknown',
                    'qty_received' => $item->qty_received_good,
                    'unit_price' => (float) $unitPrice,
                    'total' => $item->qty_received_good * $unitPrice,
                ];
            }),
            'notes' => $this->notes,
        ];
    }
}