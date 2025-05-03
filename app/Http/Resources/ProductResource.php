<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'description' => $this->description ?? null,
            'brand' => $this->brand ?? null,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'stock' => $this->stock ?? 0,
            'stock_alert' => $this->stock_alert ?? 0,
            'discount' => $this->discount ?? 0,
            'weight' => $this->weight ?? null,
            'volume' => $this->volume ?? null,
            'images' => $this->images ? asset('storage/' . $this->images) : asset('storage/product-images/product-default.jpg'),
            'unit_id' => $this->unit_id,
            'category_id' => $this->category_id,
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'taxes' => TaxResource::collection($this->whenLoaded('taxes')),

        ];
    }

}
