<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "company_name" => $this->company_name,
            "email" => $this->email,
            "phone" => $this->phone,
            "city" =>$this->city,
            "postal_code" =>$this->postal_code,
            "province" =>$this->province,
            "country" =>$this->country,
            "address" =>$this->address,
            "bank_account" => $this->bank_account,
            "bank_name"  => $this->bank_name,
            "npwp_number" => $this->npwp_number ?? null,
            "siup_number" => $this->siup_number ?? null,
            "nib_number" => $this->nib_number ?? null,
            "business_type" => $this->business_type,
            "note" => $this->note ?? '-',

            // BUAT DROPDOWN DOANG
            "label" => "{$this->name} - {$this->company_name} ({$this->email})"
        ];

    }
}
