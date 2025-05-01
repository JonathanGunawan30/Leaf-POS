<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreatePurchasePaymentRequest extends FormRequest
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
            "payment_date" => ["required", "date"],
            "amount" => ["required", "numeric", "min:1"],
            "due_date"=> ["nullable", "date"],
            "payment_method" => ["required", "string", "max:50"],
            "status" => ["required", "string", "in:paid,unpaid,failed"],
            'note' => ['nullable', 'string', 'max:500'],
            'invoice_number' => ['required', 'string', 'max:100'],
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
