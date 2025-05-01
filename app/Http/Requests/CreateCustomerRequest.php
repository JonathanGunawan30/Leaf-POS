<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateCustomerRequest extends FormRequest
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
            "name" => ["required", "string", "max:100", "min:2"],
            "company_name" => ["nullable", "string", "max:255"],
            "email" => ["required", "email", "max:255", "unique:customers,email"],
            "phone" => ["required", "string", "max:20"],
            "city" => ["required", "string", "max:100"],
            "postal_code" => ["required", "string", "max:10"],
            "province" => ["required", "string", "max:100"],
            "country" => ["required", "string", "max:100"],
            "address" => ["required", "string", "max:255"],
            "bank_account" => ["required", "string", "max:100"],
            "bank_name" => ["required", "string", "max:100"],
            "npwp_number" => ["nullable", "string", "max:50"],
            "siup_number" => ["nullable", "string", "max:50"],
            "nib_number" => ["nullable", "string", "max:50"],
            "business_type" => ["nullable", "string", "max:100"],
            "note" => ["nullable", "string", "max:255"],
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
