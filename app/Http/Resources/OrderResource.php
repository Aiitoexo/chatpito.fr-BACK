<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'subtotal_ht' => $this->subtotal_ht,
            'tax_amount' => $this->tax_amount,
            'total_ttc' => $this->total,
            'shipping' => [
                'name' => $this->shipping_name,
                'email' => $this->shipping_email,
                'address' => $this->shipping_address,
                'city' => $this->shipping_city,
                'zip' => $this->shipping_zip,
            ],
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'invoice' => new InvoiceResource($this->whenLoaded('invoice')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
