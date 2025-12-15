<?php

namespace Database\Factories;

use App\Models\Neighborhood;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Neighborhood>
 */
class NeighborhoodFactory extends Factory
{
    protected $model = Neighborhood::class;

    public function definition(): array
    {
        return [
            'name' => fake()->streetName(),
            'slug' => fake()->unique()->slug(),
            'default_due_day' => fake()->numberBetween(1, 28),
            'active' => true,
        ];
    }
}
