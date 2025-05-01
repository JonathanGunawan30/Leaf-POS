<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('product');

        return [
            'name' => 'sometimes|required|string|max:255',
            'sku' => 'sometimes|nullable|string|max:100|unique:products,sku,' . $productId,
            'purchase_price' => 'sometimes|required|numeric|min:0',
            'selling_price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|nullable|integer|min:0',
            'stock_alert' => 'sometimes|nullable|integer|min:0',
            'unit_id' => 'sometimes|nullable|exists:units,id',
            'description' => 'sometimes|nullable|string',
            'brand' => 'sometimes|nullable|string|max:100',
            'discount' => 'sometimes|nullable|numeric|min:0|max:100',
            'weight' => 'sometimes|nullable|numeric|min:0',
            'volume' => 'sometimes|nullable|numeric|min:0',
            'barcode' => 'sometimes|nullable|string|max:100|unique:products,barcode,' . $productId,
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'supplier_id' => 'sometimes|nullable|exists:suppliers,id',
            'images' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',

            'taxes' => 'sometimes|array',
            'taxes.*' => 'required|exists:taxes,id',
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
