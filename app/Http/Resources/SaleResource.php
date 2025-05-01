<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'invoice_number' => $this->invoice_number,
            'invoice_issue_date' => $this->invoice_issue_date,
            'invoice_downpayment_number' => $this->invoice_downpayment_number,
            'invoice_downpayment_issue_date' => $this->invoice_downpayment_issue_date,
            'delivery_number' => $this->delivery_number,
            'sale_date' => $this->sale_date,
            'total_amount' => $this->total_amount,
            'total_discount' => $this->total_discount,
            'total_tax' => $this->total_tax,
            'grand_total' => $this->grand_total,
            'payment_status' => $this->payment_status,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'note' => $this->note,
            'user_id' => $this->user_id,
            'user' => new UserRoleResource($this->whenLoaded('user')),
            'customer_id' => $this->customer_id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),

            'sale_details' => SaleDetailResource::collection($this->whenLoaded('details')),
            'sale_payments' => SalePaymentResource::collection($this->whenLoaded('payments')),
            'shipments' => ShipmentResource::collection($this->whenLoaded('shipments')),

        ];
    }
}
