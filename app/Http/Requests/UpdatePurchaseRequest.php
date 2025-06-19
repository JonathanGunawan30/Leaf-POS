<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class UpdatePurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $purchase = $this->route('id');

        return [
            "invoice_number" => ["sometimes", "required", "string", "max:100", "unique:purchases,invoice_number," . $purchase],
            "purchase_date" => ["sometimes", "required", "date"],
//            "total_amount" => ["sometimes", "required", "numeric", "min:0"],
            "total_discount" => ["sometimes", "required", "numeric", "min:0"],
            "shipping_amount" => ["sometimes", "required", "numeric", "min:0"],
            "status" => ["sometimes", "nullable", "in:pending,confirmed,shipped,delivered,cancelled", "string", "lowercase"],
            "due_date" => ["sometimes", "nullable", "date"],
            "estimated_arrival_date" => ["sometimes", "required", "date"],
            "actual_arrival_date" => ["sometimes", "nullable", "date"],
            "supplier_id" => ["sometimes", "required", "integer", "exists:suppliers,id"],

            // For relationships, you might want to handle updates separately or use a different strategy
            // Option 1: Allow full replacement of related items
            'purchase_details' => ['sometimes', 'array', 'min:1'],
            'purchase_details.*.id' => ['sometimes', 'integer', 'exists:purchase_details,id,purchase_id,' . $purchase], // For existing records
            'purchase_details.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'purchase_details.*.quantity' => ['required', 'integer', 'min:1'],
//            'purchase_details.*.unit_price' => ['required', 'numeric', 'min:0'],
//            'purchase_details.*.sub_total' => ['required', 'numeric', 'min:0'],
            'purchase_details.*.note' => ['nullable', 'string', 'max:500'],

            'purchase_payments' => ['sometimes', 'array'],
            'purchase_payments.*.id' => ['sometimes', 'integer', 'exists:purchase_payments,id,purchase_id,' . $purchase], // For existing records
            'purchase_payments.*.payment_date' => ['required', 'date'],
            'purchase_payments.*.amount' => ['required', 'numeric', 'min:0'],
            'purchase_payments.*.due_date' => ['nullable', 'date'],
            'purchase_payments.*.payment_method' => ['required', "string", "max:50"],
            'purchase_payments.*.status' => ['required', 'in:unpaid,paid,failed'],
            'purchase_payments.*.note' => ['nullable', 'string', 'max:500'],

            'taxes' => ['sometimes', 'array'],
            'taxes.*.id' => ['sometimes', 'integer', 'exists:purchase_tax,purchase_id,' . $purchase . ',tax_id,taxes.*.tax_id'], // For existing records
            'taxes.*.tax_id' => ['required', 'exists:taxes,id'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => [
                "message" => $validator->getMessageBag()
            ]
        ], 400));
    }
}
