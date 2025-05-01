<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
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
            "name" => ["sometimes", "string", "min:3", "max:100"],
            "company_name" => ["sometimes", "string", "min:2", "max:255"],
            "email" => [
                "sometimes", "email", "string", "max:255",
                Rule::unique('suppliers', 'email')->ignore($this->route('id'))
            ],
            "phone" => ["sometimes", "string", "min:5", "max:20"],
            "city" => ["sometimes", "string", "min:3", "max:100"],
            "postal_code" => ["sometimes", "string", "min:2", "max:10"],
            "province" => ["sometimes", "string", "min:3", "max:100"],
            "country" => ["sometimes", "string", "min:3", "max:100"],
            "address" => ["sometimes", "string", "min:5", "max:255"],
            "bank_account" => ["sometimes", "string", "min:5", "max:50"],
            "bank_name" => ["sometimes", "string", "min:2", "max:100"],
            "npwp_number" => ["sometimes", "nullable", "string", "max:50"],
            "siup_number" => ["sometimes", "nullable", "string", "max:50"],
            "nib_number" => ["sometimes", "nullable", "string", "max:50"],
            "business_type" => ["sometimes", "string", "min:3", "max:100"],
            "note" => ["sometimes", "nullable", "string", "min:2", "max:1000"],
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
