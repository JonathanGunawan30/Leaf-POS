<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
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
            'sale_id' => $this->sale_id,
            'courier_id' => $this->courier_id,
            'vehicle_type' => $this->vehicle_type,
            'vehicle_number' => $this->vehicle_number,
            'shipping_date' => $this->shipping_date,
            'estimated_arrival_date' => $this->estimated_arrival_date,
            'actual_arrival_date' => $this->actual_arrival_date,
            'shipping_cost' => $this->shipping_cost,
            'status' => $this->status,
            'note' => $this->note,
            'courier' => new CourierResource($this->whenLoaded('courier')),
        ];
    }
}
