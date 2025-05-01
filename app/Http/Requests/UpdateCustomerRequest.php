<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
            "name" => ["sometimes", "string", "max:100", "min:2"],
            "company_name" => ["sometimes", "string", "max:255"],
            "email" => ["sometimes", "email", "max:255", Rule::unique('customers', 'email')->ignore($this->route('id'))],
            "phone" => ["sometimes", "string", "max:20"],
            "city" => ["sometimes", "string", "max:100"],
            "postal_code" => ["sometimes", "string", "max:10"],
            "province" => ["sometimes", "string", "max:100"],
            "country" => ["sometimes", "string", "max:100"],
            "address" => ["sometimes", "string", "max:255"],
            "bank_account" => ["sometimes", "string", "max:100"],
            "bank_name" => ["sometimes", "string", "max:100"],
            "npwp_number" => ["sometimes", "string", "max:50"],
            "siup_number" => ["sometimes", "string", "max:50"],
            "nib_number" => ["sometimes", "string", "max:50"],
            "business_type" => ["sometimes", "string", "max:100"],
            "note" => ["sometimes", "string", "max:255"],
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
