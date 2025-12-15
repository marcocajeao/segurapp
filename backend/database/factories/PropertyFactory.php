<?php

namespace Database\Factories;

use App\Models\Neighborhood;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'neighborhood_id' => Neighborhood::factory(),
            'beneficiary_id' => null,
            'code' => strtoupper(Str::random(6)),
            'street' => fake()->streetName(),
            'number' => (string) fake()->buildingNumber(),
            'extra_address' => null,
            'latitude' => fake()->latitude(-34.7, -34.2),
            'longitude' => fake()->longitude(-58.7, -58.1),
            'is_beneficiary' => true,
            'qr_token' => Str::uuid()->toString(),
            'active' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->state([
            'active' => false,
        ]);
    }
}
