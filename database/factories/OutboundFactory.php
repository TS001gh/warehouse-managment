<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Outbound>
 */
class OutboundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::inRandomOrder()->first()->id,
            'quantity' => $this->faker->numberBetween(1, 20),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'customer_id' => Customer::inRandomOrder()->first()->id,
        ];
    }
}
