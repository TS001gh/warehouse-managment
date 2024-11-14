<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inbound>
 */
class InboundFactory extends Factory
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
            'quantity' => $this->faker->numberBetween(5, 50),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'supplier_id' => Supplier::inRandomOrder()->first()->id,
        ];
    }
}
