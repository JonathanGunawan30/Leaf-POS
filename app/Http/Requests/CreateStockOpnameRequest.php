<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateStockOpnameRequest extends FormRequest
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
            "opname_date" => ["nullable", "date"],
            "status" => ["nullable", "in:draft,submitted,approved,rejected"],
            "notes" => ["nullable", "string", "max:500"],
            "location" => ["required", "string", "max:255"],
            "approved_by" => [
                "nullable",
                "string",
                "max:255",
                Rule::requiredIf(function () {
                    return request()->input('status') === 'approved';
                })
            ],

            "items" => ["required", "array", "min:1"],
            "items.*.product_id" => ["required", "exists:products,id"],
            "items.*.actual_stock" => ["required", "integer", "min:0"],
            "items.*.notes" => ["nullable", "string", "max:500"],
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
