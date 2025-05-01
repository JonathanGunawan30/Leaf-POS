<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateSupplierRequest extends FormRequest
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
            "name" => ["required", "string", "max:100", "min:3"],
            "company_name" => ["required", "string", "max:255", "min:2"],
            "email" => ["required", "email", "string", "max:255", "unique:suppliers,email"],
            "phone" => ["required", "string", "max:20", "min:5"],
            "city" => ["required", "string", "max:100", "min:3"],
            "postal_code" => ["required", "string", "max:10", "min:2"],
            "province" => ["required", "string", "max:100", "min:3"],
            "country" => ["required", "string", "max:100", "min:3"],
            "address" => ["required", "string", "max:255", "min:5"],
            "bank_account" => ["required", "string", "max:50", "min:5"],
            "bank_name" => ["required", "string", "max:100", "min:2"],
            "npwp_number" => ["nullable", "string", "max:50"],
            "siup_number" => ["nullable", "string", "max:50"],
            "nib_number" => ["nullable", "string", "max:50"],
            "business_type" => ["required", "string", "max:100", "min:3"],
            "note" => ["nullable", "string", "max:1000", "min:2"],
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
