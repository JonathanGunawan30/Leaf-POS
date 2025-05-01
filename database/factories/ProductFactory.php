<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'sku' => $this->faker->unique()->bothify('SKU-####'),
            'purchase_price' => $this->faker->randomFloat(2, 1000, 10000),
            'selling_price' => $this->faker->randomFloat(2, 11000, 20000),
            'stock' => $this->faker->numberBetween(10, 100),
            'stock_alert' => 5,
            'unit_id' => Unit::factory(),
            'description' => $this->faker->sentence,
            'brand' => $this->faker->company,
            'discount' => $this->faker->numberBetween(0, 20),
            'weight' => $this->faker->randomFloat(2, 0.1, 2),
            'volume' => $this->faker->randomFloat(2, 1, 10),
            'barcode' => $this->faker->ean13,
            'category_id' => Category::factory(),
            'images' => 'product-images/product-default.jpg',
        ];
    }
}
