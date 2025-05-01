<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSaleRequest extends FormRequest
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
            "sale_date" => ["sometimes", "date"],
            "total_discount" => ["sometimes", "numeric"],
            "status" => ["sometimes", "in:pending,confirmed,shipped,delivered,cancelled"],
            "due_date" => ["nullable", "date"],
            "note" => ["nullable", "string", "max:500"],
            "customer_id" => ["sometimes", "integer"],

            "sale_details" => ["sometimes", "array", "min:1"],
            "sale_details.*.product_id" => ["sometimes", "integer"],
            "sale_details.*.quantity" => ["sometimes", "integer", "min:1"],
//            "sale_details.*.unit_price" => ["sometimes", "numeric", "min:0"],
            "sale_details.*.note" => ["nullable", "string", "max:500"],

            "sale_payments" => ["sometimes", "array"],
            "sale_payments.*.payment_date" => ["sometimes", "date"],
            "sale_payments.*.amount" => ["sometimes", "numeric", "min:0"],
            "sale_payments.*.due_date" => ["nullable", "date"],
            "sale_payments.*.status" => ["sometimes", "in:unpaid,paid,failed"],
            "sale_payments.*.note" => ["nullable", "string", "max:500"],
            "sale_payments.*.payment_method" => ["sometimes", "in:cash,bank_transfer,giro"],

            "shipments" => ["nullable", "array"],
            'shipments.*.id' => 'sometimes',
            "shipments.*.courier_id" => ["sometimes"],
            "shipments.*.vehicle_type" => ["sometimes", "in:motorcycle,car_sedan,car_van,car_pickup,truck_small,truck_medium,truck_large,other"],
            "shipments.*.vehicle_number" => ["sometimes", "max:15"],
            "shipments.*.shipping_date" => ["sometimes", "date"],
            "shipments.*.estimated_arrival_date" => ["sometimes", "date"],
            "shipments.*.actual_arrival_date" => ["nullable", "date"],
            "shipments.*.status" => ["sometimes", "in:pending,shipped,delivered,cancelled,processing,in transit,on hold,delivered partially"],
            "shipments.*.note" => ["nullable", "string", "max:500"],
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
