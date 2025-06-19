<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdatePurchaseReturnRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            "invoice_number" => ["sometimes", "required", "string", Rule::exists('purchases', 'invoice_number')->whereNull('deleted_at'), "unique:purchase_returns,invoice_number,{$id}"],

            "return_date" => ["sometimes", "required", "date", "before_or_equal:today"],
            "reason" => ["sometimes", "required", "string", "max:255"],
            "status" => ["sometimes", "required", "string", "lowercase", Rule::in(['pending', 'approved', 'rejected', 'completed', 'cancelled'])],
            "payment_status" => ["sometimes", "required", "string", "lowercase", Rule::in(['unpaid', 'paid', 'partially_paid', 'failed'])],
            "compensation_method" => ["sometimes", "required", Rule::in(['refund', 'replacement'])],

            "purchase_return_details" => ["sometimes", "required", "array", "min:1"],
            "purchase_return_details.*.product_id" => ["required_with:purchase_return_details", "integer", Rule::exists('products', 'id')->whereNull('deleted_at')],
            "purchase_return_details.*.quantity" => ["required_with:purchase_return_details", "integer", "min:1"],
            "purchase_return_details.*.unit_price" => ["nullable", "numeric", "min:0"],
            "purchase_return_details.*.sub_total" => ["nullable", "numeric", "min:0"],
            "purchase_return_details.*.note" => ["nullable", "string", "max:500"],

            "purchase_return_payments" => ["sometimes", "array"],
            "purchase_return_payments.*.payment_date" => ["required_with:purchase_return_payments", "date"],
            "purchase_return_payments.*.amount" => ["required_with:purchase_return_payments", "numeric", "min:0"],
            "purchase_return_payments.*.due_date" => ["nullable", "date"],
            "purchase_return_payments.*.payment_method" => ["required_with:purchase_return_payments", "string", Rule::in(['cash', 'bank_transfer', 'giro', 'paypal', 'e_wallet', 'qris'])],
            "purchase_return_payments.*.status" => ["required_with:purchase_return_payments", "string", Rule::in(['unpaid', 'paid', 'failed'])],
            "purchase_return_payments.*.note" => ["nullable", "string", "max:500"],

            "purchase_return_taxes" => ["sometimes", "array"],
            "purchase_return_taxes.*.tax_id" => ["required_with:purchase_return_taxes", "integer", Rule::exists('taxes', 'id')->whereNull('deleted_at')],
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
