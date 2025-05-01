<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'company_name' => $this->faker->company,
            'email' => $this->faker->companyEmail,
            'phone' => $this->faker->phoneNumber,
            'city' => $this->faker->city,
            'postal_code' => $this->faker->postcode,
            'province' => $this->faker->state,
            'country' => $this->faker->country,
            'address' => $this->faker->address,
            'bank_account' => $this->faker->bankAccountNumber,
            'bank_name' => $this->faker->company,
            'npwp_number' => $this->faker->numerify('##.###.###.#-###.###'),
            'siup_number' => $this->faker->bothify('SIUP-####'),
            'nib_number' => $this->faker->numerify('############'),
            'business_type' => $this->faker->word,
            'note' => $this->faker->sentence,
        ];
    }
}
