<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreExpenseRequest extends FormRequest
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
            "description" => ["sometimes", "nullable", "string", "max:500"],
            "expense_date" => ["required", "date"],
            "note" => ["sometimes", "nullable", "string", "max:500"],
            "amount" => ["required", "numeric", "min:0"],
            "category_id" => ["required", "integer", "exists:expense_categories,id"],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "errors" => [
                "message" => $validator->getMessageBag()
            ]
        ], 400));
    }
}
