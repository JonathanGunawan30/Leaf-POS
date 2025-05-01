<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchasePaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "payment_date" => $this->payment_date,
            "amount" => $this->amount,
            "due_date" => $this->due_date ?? "-",
            "payment_method" => $this->payment_method,
            "status" => $this->status,
            "note" => $this->note ?? "-",
            "invoice_number" => $this->purchase->invoice_number,
        ];
    }
}
