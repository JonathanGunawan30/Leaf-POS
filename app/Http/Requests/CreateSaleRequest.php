<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateSaleRequest extends FormRequest
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
            "sale_date" => ["required", "date"],
            "total_discount" => ["required", "numeric"],
            "status" => ["required", "in:pending,confirmed,shipped,delivered,cancelled"],
            "due_date" => ["nullable", "date"],
            "note" => ["nullable", "string", "max:500"],
            "customer_id" => ["required", "integer"],

            "sale_details" => ["required", "array", "min:1"],
            "sale_details.*.product_id" => ["required", "integer"],
            "sale_details.*.quantity" => ["required", "integer", "min:1"],
//            "sale_details.*.unit_price" => ["required", "numeric", "min:0"],
            "sale_details.*.note" => ["nullable", "string", "max:500"],

            "sale_payments" => ["required", "array"],
            "sale_payments.*.payment_date" => ["required", "date"],
            "sale_payments.*.amount" => ["required", "numeric", "min:0"],
            "sale_payments.*.due_date" => ["nullable", "date"],
            "sale_payments.*.status" => ["required", "in:unpaid,paid,failed"],
            "sale_payments.*.note" => ["nullable", "string", "max:500"],
            "sale_payments.*.payment_method" => ["required", "in:cash,bank_transfer,giro"],

            "shipments" => ["nullable", "array"],
            "shipments.*.courier_id" => ["required"],
            "shipments.*.vehicle_type" => ["required", "in:motorcycle,car_sedan,car_van,car_pickup,truck_small,truck_medium,truck_large,other"],
            "shipments.*.vehicle_number" => ["required", "max:15"],
            "shipments.*.shipping_date" => ["required", "date"],
            "shipments.*.estimated_arrival_date" => ["required", "date"],
            "shipments.*.actual_arrival_date" => ["nullable", "date"],
            "shipments.*.status" => ["required", "in:pending,shipped,delivered,cancelled,processing, in transit, on hold, delivered partially"],
            "shipments.*.note" =>["nullable", "string", "max:500"]
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
