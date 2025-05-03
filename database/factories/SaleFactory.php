<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\SaleDetail;
use App\Models\SalePayment;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $total_amount = $this->faker->randomFloat(2, 500000, 5000000);
        $total_tax = $total_amount * 0.11; // 11% tax
        $total_discount = $this->faker->randomFloat(2, 0, 500000);
        $grand_total = $total_amount + $total_tax - $total_discount;

        return [
            'invoice_number' => 'INV-' . $this->faker->unique()->numberBetween(1000, 9999),
            'invoice_issue_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'delivery_number' => 'DEL-' . $this->faker->numberBetween(1000, 9999),
            'sale_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'total_amount' => $total_amount,
            'total_tax' => $total_tax,
            'total_discount' => $total_discount,
            'grand_total' => $grand_total,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'shipped', 'delivered', 'completed']),
            'payment_status' => $this->faker->randomElement(['unpaid', 'partially_paid', 'paid']),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'customer_id' => Customer::inRandomOrder()->first()->id ?? Customer::factory(),
            'note' => $this->faker->paragraph(1),
        ];
    }

    /**
     * Configure the model factory to create a sale with a confirmed status.
     *
     * @return $this
     */
    public function confirmed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'confirmed',
            ];
        });
    }

    /**
     * Configure the model factory to create a sale with a completed status.
     *
     * @return $this
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'payment_status' => 'paid',
            ];
        });
    }

    /**
     * Configure the model factory to create a sale with sale details.
     *
     * @param int $count Number of detail items to create
     * @return $this
     */
    public function withDetails($count = 2)
    {
        return $this->afterCreating(function (Sale $sale) use ($count) {
            $details = [];
            $total_amount = 0;

            // Create sale detail items
            for ($i = 0; $i < $count; $i++) {
                $product = Product::inRandomOrder()->first() ?? Product::factory()->create();
                if ($product->taxes()->count() === 0) {
                    $tax = \App\Models\Tax::inRandomOrder()->first() ?? \App\Models\Tax::factory()->create();
                    $product->taxes()->attach($tax->id, [
                        'amount' => $product->unit_price * ($tax->percentage / 100)
                    ]);
                }
                $quantity = $this->faker->numberBetween(1, 10);
                $unit_price = $product->unit_price ?? $this->faker->randomFloat(2, 10000, 500000);
                $sub_total = $quantity * $unit_price;

                $detail = SaleDetail::factory()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'sub_total' => $sub_total,
                ]);

                $details[] = $detail;
                $total_amount += $sub_total;
            }

            // Update the sale totals based on the details
            $total_tax = $total_amount * 0.11; // 11% tax
            $total_discount = $sale->total_discount ?? 0;
            $grand_total = $total_amount + $total_tax - $total_discount;

            $sale->update([
                'total_amount' => $total_amount,
                'total_tax' => $total_tax,
                'grand_total' => $grand_total,
            ]);
        });
    }

    /**
     * Configure the model factory to create a sale with payments.
     *
     * @param int $count Number of payments to create
     * @return $this
     */
    public function withPayments($count = 1)
    {
        return $this->afterCreating(function (Sale $sale) use ($count) {
            // If multiple payments, divide the amount
            $payment_amount = $sale->grand_total / $count;

            for ($i = 0; $i < $count; $i++) {
                SalePayment::factory()->create([
                    'sale_id' => $sale->id,
                    'payment_date' => $this->faker->dateTimeBetween($sale->sale_date, 'now'),
                    'amount' => $payment_amount,
                    'payment_method' => $this->faker->randomElement(['cash', 'bank_transfer', 'giro']),
                    'due_date' => $this->faker->dateTimeBetween('now', '+2 weeks'),
                    'status' => 'paid',
                    'note' => $this->faker->sentence(),
                ]);
            }

            // Update payment status if all payments are made
            $sale->update([
                'payment_status' => 'paid',
            ]);
        });
    }

    /**
     * Configure the model factory to create a sale with a shipment.
     *
     * @return $this
     */
    public function withShipments()
    {
        return $this->afterCreating(function (Sale $sale) {
            $shipping_date = $this->faker->dateTimeBetween($sale->sale_date, '+3 days');
            $estimated_arrival = $this->faker->dateTimeBetween($shipping_date, '+7 days');

            Shipment::factory()->create([
                'sale_id' => $sale->id,
                'courier_id' => Courier::inRandomOrder()->first()->id ?? Courier::factory(),
                'shipping_date' => $shipping_date,
                'estimated_arrival_date' => $estimated_arrival,
                'actual_arrival_date' => $this->faker->optional(0.7)->dateTimeBetween($estimated_arrival, '+10 days'),
                'status' => $this->faker->randomElement(['pending', 'shipped', 'in transit', 'delivered']),
                'shipping_cost' => $this->faker->randomFloat(2, 10000, 100000),
                'note' => $this->faker->sentence(),
                'vehicle_type' => $this->faker->randomElement(['motorcycle', 'car_sedan', 'car_van', 'car_pickup', 'truck_small', 'truck_medium', 'truck_large']),
                'vehicle_number' => strtoupper($this->faker->bothify('?####???')),
            ]);

            // Update sale status if shipment is created
            if (!in_array($sale->status, ['shipped', 'delivered', 'completed'])) {
                $sale->update([
                    'status' => 'shipped',
                ]);
            }
        });
    }
}
