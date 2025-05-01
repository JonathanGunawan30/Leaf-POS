<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'company_name' => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'city' => $this->faker->city,
            'postal_code' => $this->faker->postcode,
            'province' => $this->faker->state,
            'country' => $this->faker->country,
            'address' => $this->faker->address,
            'bank_account' => $this->faker->bankAccountNumber,
            'bank_name' => $this->faker->randomElement(['BCA', 'BNI', 'Mandiri', 'BRI']),
            'npwp_number' => $this->faker->optional()->regexify('[0-9]{2}\.[0-9]{3}\.[0-9]{3}\.[0-9]-[0-9]{3}\.[0-9]{3}'),
            'siup_number' => $this->faker->optional()->bothify('SIUP-##########'),
            'nib_number' => $this->faker->optional()->bothify('NIB-##########'),
            'business_type' => $this->faker->optional()->randomElement(['Distributor', 'Retail', 'Supplier']),
            'note' => $this->faker->optional()->sentence,
        ];
    }
}
