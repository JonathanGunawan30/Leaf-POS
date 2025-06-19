<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreatePurchaseReturnRequest extends FormRequest
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
        return [
            "invoice_number" => ["required", "string", Rule::exists('purchases', 'invoice_number')->whereNull('deleted_at'), "unique:purchase_returns,invoice_number"],
            "return_date" => ["required", "date", "before_or_equal:today"],
            "reason" => ["required", "string", "max:255"],
            "status" => ["required", "string", "lowercase", Rule::in(['pending', 'approved', 'rejected', 'completed', 'cancelled'])],
            "payment_status" => ["required", "string", "lowercase", Rule::in(['unpaid', 'paid', 'partially_paid', 'failed'])],
            'compensation_method' => ['required', Rule::in(['refund', 'replacement'])],


            'purchase_return_details' => ['required', 'array', 'min:1'],
            'purchase_return_details.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'purchase_return_details.*.quantity' => ['required', 'integer', 'min:1'],
            'purchase_return_details.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'purchase_return_details.*.sub_total' => ['nullable', 'numeric', 'min:0'],
            'purchase_return_details.*.note' => ['nullable', 'string', 'max:500'],


            'purchase_return_payments' => ['nullable', 'array'],
            'purchase_return_payments.*.payment_date' => ['required', 'date'],
            'purchase_return_payments.*.amount' => ['required', 'numeric', 'min:0'],
            'purchase_return_payments.*.due_date' => ['nullable', 'date'],
            'purchase_return_payments.*.payment_method' => ['required', "string", Rule::in(['cash', 'bank_transfer', 'giro', 'paypal', 'e_wallet', 'qris'])],
            'purchase_return_payments.*.status' => ['required', 'string', Rule::in(['unpaid', 'paid', 'failed'])],
            'purchase_return_payments.*.note' => ['nullable', 'string', 'max:500'],


            'purchase_return_taxes' => ['nullable', 'array'],
            'purchase_return_taxes.*.tax_id' => ['required', 'integer', Rule::exists('taxes', 'id')->whereNull('deleted_at')],
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
