<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseReturnResource extends JsonResource
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
            'invoice_number_returns' => $this->invoice_number_returns,
            'delivery_number_returns' => $this->delivery_number_returns,
            'return_date' => $this->return_date,
            'due_date' => $this->due_date,
            'total_amount' => $this->total_amount,
            'total_discount' => $this->total_discount,
            'shipping_amount' => $this->shipping_amount,
            'grand_total' => $this->grand_total,
            'status' => $this->status,
            'reason' => $this->reason,
            'compensation_method' => $this->compensation_method,
            'payment_status' => $this->payment_status,
            'estimated_arrival_date' => $this->estimated_arrival_date,
            'actual_arrival_date' => $this->actual_arrival_date,


            'user' => new UserRoleResource($this->whenLoaded('user')),

            'supplier' => new SupplierResource($this->whenLoaded('supplier')),

            'purchase_details' => $this->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'product_name' => $detail->product?->name,
                    'unit_code' => $detail->product?->unit?->code,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'sub_total' => $detail->sub_total,
                    'note' => $detail->note,
                ];
            }),


            'purchase_return_payments' => $this->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'payment_date' => $payment->payment_date,
                    'amount' => $payment->amount,
                    'due_date' => $payment->due_date,
                    'payment_method' => $payment->payment_method,
                    'status' => $payment->status,
                    'note' => $payment->note,
                ];
            }),

            'taxes' => $this->taxes->map(function ($tax) {
                return [
                    'id' => $tax->id,
                    'name' => $tax->name,
                    'rate' => $tax->rate,
                    'amount' => $tax->pivot->amount ?? 0,
                ];
            }),
        ];
    }
}
