<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalePaymentResource extends JsonResource
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
            'payment_date' => $this->payment_date,
            'amount' => $this->amount,
            'due_date' => $this->due_date,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'note' => $this->note,
        ];
    }
}
