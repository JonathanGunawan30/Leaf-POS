<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateStockOpnameRequest extends FormRequest
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
            "opname_date" => ["sometimes", "date"],
            "status" => [
                "sometimes",
                "string",
                "in:draft,submitted,approved,rejected",
                function ($attribute, $value, $fail) {
                    if ($value === 'approved' && empty(request()->input('approved_by'))) {
                        $fail('The approved by field is required when status is approved.');
                    }
                }
            ],
            "notes" => ["sometimes", "string", "max:500"],
            "location" => ["sometimes", "string", "max:255"],
            "approved_by" => [
                "sometimes",
                "string",
                "max:255",
                Rule::requiredIf(function () {
                    return request()->input('status') === 'approved';
                })
            ],

            "items" => ["sometimes", "array", "min:1"],
            "items.*.product_id" => ["required_with:items", "exists:products,id"],
            "items.*.actual_stock" => ["required_with:items", "integer", "min:0"],
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
