<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockOpnameResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', function() {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'approved_by' => $this->approved_by,
            'location' => $this->location,
            'opname_date' => $this->opname_date,
            'status' => $this->status,
            'notes' => $this->notes,
            'items' => StockOpnameItemResource::collection($this->whenLoaded('items')),
            'total_items' => $this->items->count(),

        ];
    }
}
