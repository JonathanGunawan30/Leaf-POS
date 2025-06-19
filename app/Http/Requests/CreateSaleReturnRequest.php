<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateSaleReturnRequest extends FormRequest
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
            "return_date" => ["required", "date"],
            "total_discount" => ["required", "numeric"],
            "status" => ["required", "in:pending,confirmed,shipped,delivered,cancelled"],
            "payment_status" => ["required", "in:unpaid,paid,partially_paid,failed"],
            "reason" => ["required", "string", "max:500"],
            "invoice_number" => ["required", "string", "max:100", "unique:sale_returns,invoice_number", "exists:sales,invoice_number"],
            "compensation_method" => ["required", "in:refund,replacement"],

            "sale_return_details" => ["required", "array", "min:1"],
            "sale_return_details.*.product_id" => ["required", "integer"],
            "sale_return_details.*.quantity" => ["required", "integer", "min:1"],
            "sale_return_details.*.note" => ["nullable", "string", "max:500"],

            "sale_payments" => ["required", "array"],
            "sale_payments.*.payment_date" => ["required", "date"],
            "sale_payments.*.amount" => ["required", "numeric", "min:0"],
            "sale_payments.*.due_date" => ["nullable", "date"],
            "sale_payments.*.status" => ["required", "in:unpaid,paid,failed"],
            "sale_payments.*.note" => ["nullable", "string", "max:500"],
            "sale_payments.*.payment_method" => ["required", "in:cash,bank_transfer,giro"],

            "shipment_returns" => ["nullable", "array"],
            "shipment_returns.*.courier_id" => ["required"],
            "shipment_returns.*.vehicle_type" => ["required", "in:motorcycle,car_sedan,car_van,car_pickup,truck_small,truck_medium,truck_large,other"],
            "shipment_returns.*.vehicle_number" => ["required", "max:15"],
            "shipment_returns.*.shipping_date" => ["required", "date"],
            "shipment_returns.*.estimated_arrival_date" => ["required", "date"],
            "shipment_returns.*.actual_arrival_date" => ["nullable", "date"],
            "shipment_returns.*.status" => ["required", "in:pending,shipped,delivered,cancelled,processing, in transit, on hold, delivered partially"],
            "shipment_returns.*.note" =>["nullable", "string", "max:500"]
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
