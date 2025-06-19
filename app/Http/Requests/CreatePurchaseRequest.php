<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreatePurchaseRequest extends FormRequest
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
        $purchaseId = $this->route('purchase') ? $this->route('purchase')->id : null;

        return [
            "invoice_number" => [
                "required",
                "string",
                "max:100",
                Rule::unique('purchases', 'invoice_number')->ignore($purchaseId)
            ],
            "purchase_date" => ["required", "date"],
//            "total_amount" => ["required", "numeric", "min:0"],
            "total_discount" => ["required", "numeric", "min:0"],
            "shipping_amount" => ["required", "numeric", "min:0"],
            "status" => ["nullable", "in:pending,confirmed,shipped,delivered,cancelled", "string", "lowercase"],
//            "payment_status" => ["nullable", "in:unpaid,paid,partially_paid,failed", "string", "lowercase"],
            "due_date" => ["nullable", "date"],
            "estimated_arrival_date" => ["required", "date"],
            "actual_arrival_date" => ["nullable", "date"],
            "supplier_id" => [
                "required",
                "integer",
                Rule::exists('suppliers', 'id')->whereNull('deleted_at')
            ],

            'purchase_details' => ['required', 'array', 'min:1'],
            'purchase_details.*.product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->whereNull('deleted_at')
            ],
            'purchase_details.*.quantity' => ['required', 'integer', 'min:1'],
//            'purchase_details.*.unit_price' => ['required', 'numeric', 'min:0'],
//            'purchase_details.*.sub_total' => ['required', 'numeric', 'min:0'],
            'purchase_details.*.note' => ['nullable', 'string', 'max:500'],

            'purchase_payments' => ['required', 'array', 'min:1'],
            'purchase_payments.*.payment_date' => ['required', 'date'],
            'purchase_payments.*.amount' => ['required', 'numeric', 'min:0'],
            'purchase_payments.*.due_date' => ['nullable', 'date'],
            'purchase_payments.*.payment_method' => ['required', "in:cash,bank_transfer,giro,paypal,e_wallet,qris"],
            'purchase_payments.*.status' => ['required', 'in:unpaid,paid,failed'],
            'purchase_payments.*.note' => ['nullable', 'string', 'max:500'],

            'taxes' => ['nullable', 'array'],
            'taxes.*.tax_id' => [
                'required',
                'integer',
                Rule::exists('taxes', 'id')->whereNull('deleted_at')
            ],
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
