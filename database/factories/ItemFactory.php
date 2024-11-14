<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
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
            'code' => $this->faker->unique()->numerify('ITM-###'),
            'min_quantity' => $this->faker->numberBetween(5, 15),
            'current_quantity' => $this->faker->numberBetween(20, 100),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'image' => $this->faker->imageUrl(),
            'is_active' => $this->faker->boolean,
            'group_id' => Group::inRandomOrder()->first()->id,
        ];
    }
}
