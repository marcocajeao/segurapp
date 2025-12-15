<?php

namespace Database\Factories;

use App\Models\Neighborhood;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $period = now()->startOfMonth();

        return [
            'neighborhood_id' => Neighborhood::factory(),
            'property_id' => Property::factory(),
            'period' => $period->toDateString(),
            'method' => Payment::METHOD_MP,
            'status' => Payment::STATUS_PENDING,
            'amount' => fake()->randomFloat(2, 1000, 10000),
            'paid_at' => null,
            'mp_payment_id' => null,
            'mp_preference_id' => null,
            'reference' => null,
            'created_by' => null,
            'reviewed_by' => null,
        ];
    }
}
