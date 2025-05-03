<?php

namespace Database\Factories;

use App\Models\Courier;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        return [
            'courier_id' => Courier::factory(),
            'vehicle_type' => 'car_van',
            'vehicle_number' => 'B1234XYZ',
            'shipping_date' => now()->toDateString(),
            'estimated_arrival_date' => now()->addDays(1)->toDateString(),
            'status' => 'delivered',
            'note' => 'Test shipment',
        ];
    }
}
