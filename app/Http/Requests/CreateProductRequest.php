<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateProductRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:products,name',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_alert' => 'nullable|integer|min:0',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:100',
            'discount' => 'nullable|numeric|min:0|max:100',
            'weight' => 'nullable|numeric|min:0',
            'volume' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'category_id' => 'required|exists:categories,id',
            'images' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'taxes' => 'required|array',
            'taxes.*.tax_id' => 'required|exists:taxes,id',
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
