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
            'subtotal_ht' => $this->subtotal_ht,
            'tax_amount' => $this->tax_amount,
            'total_ttc' => $this->total_ttc,
            'status' => $this->status,
            'issued_at' => $this->issued_at,
            'pdf_path' => $this->pdf_path,
        ];
    }
}
