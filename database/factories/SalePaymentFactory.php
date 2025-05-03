<?php

namespace Database\Factories;

use App\Models\SalePayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalePaymentFactory extends Factory
{
    protected $model = SalePayment::class;

    public function definition(): array
    {
        return [
            'payment_date' => now()->toDateString(),
            'amount' => $this->faker->randomNumber(5),
            'payment_method' => 'cash',
            'status' => 'paid',
            'note' => 'First payment',
        ];
    }
}
