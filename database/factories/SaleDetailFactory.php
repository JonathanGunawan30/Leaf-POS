<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\SaleDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleDetailFactory extends Factory
{
    protected $model = SaleDetail::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'quantity' => $this->faker->randomDigitNotNull(),
            'unit_price' => $this->faker->randomNumber(3),
            'note' => $this->faker->sentence,
        ];
    }
}
