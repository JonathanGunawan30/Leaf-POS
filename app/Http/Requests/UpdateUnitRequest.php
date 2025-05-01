<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
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
        $unitId = $this->route('id');

        return [
            'name' => [
                'sometimes',
                'string',
                'min:3',
                'max:50',
                Rule::unique('units', 'name')->ignore($unitId),
            ],
            'code' => [
                'sometimes',
                'string',
                'min:1',
                'max:10',
                Rule::unique('units', 'code')->ignore($unitId),
            ],
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
