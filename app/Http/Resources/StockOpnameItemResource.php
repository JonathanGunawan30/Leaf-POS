<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockOpnameItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
        'id' => $this->id,
        'product_id' => $this->product_id,
        'product' => $this->whenLoaded('product', function() {
            return [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'sku' => $this->product->sku ?? null,
                'barcode' => $this->product->barcode ?? null,
            ];
        }),
        'system_stock' => $this->system_stock,
        'actual_stock' => $this->actual_stock,
        'difference' => $this->difference,
        'notes' => $this->notes,
    ];
    }
}
