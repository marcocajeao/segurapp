<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Models\Property;
use App\Services\Payments\PaymentStatusService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentStatusService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(PaymentStatusService::class);
    }

    public function test_required_through_period_switches_after_due_day(): void
    {
        $today = Carbon::create(2025, 3, 4);
        $dueDay = 5;

        $beforeDue = $this->service->requiredThroughPeriod($today, $dueDay);
        $afterDue  = $this->service->requiredThroughPeriod(Carbon::create(2025, 3, 7), $dueDay);

        $this->assertTrue($beforeDue->equalTo(Carbon::create(2025, 2, 1)));
        $this->assertTrue($afterDue->equalTo(Carbon::create(2025, 3, 1)));
    }

    public function test_property_is_up_to_date_only_when_all_required_periods_are_approved(): void
    {
        $dueDay = 5;

        // Alta 15/01/2025 => debe Enero
        $property = $this->propertyRegisteredAt(Carbon::create(2025, 1, 15));

        // 12/04/2025 (>=6) => exige Abril
        $today = Carbon::create(2025, 4, 12);

        $start = $this->service->registrationStartPeriod($property)->startOfMonth();
        $end   = $this->service->requiredThroughPeriod($today, $dueDay)->startOfMonth();
        $expectedPeriods = $this->service->expectedPeriods($start, $end);

        // Creamos pagos con create() directo (evita efectos del factory)
        foreach ($expectedPeriods as $period) {
            $this->createApprovedPayment($property, $period);
        }

        // Confirmación: DB tiene los pagos (comparación segura con whereDate)
        foreach ($expectedPeriods as $period) {
            $this->assertTrue(
                Payment::query()
                    ->where('property_id', $property->id)
                    ->where('status', Payment::STATUS_APPROVED)
                    ->whereDate('period', $period)
                    ->exists(),
                "Payment for period {$period} should exist and be approved"
            );
        }

        $this->assertTrue(
            $this->service->isUpToDate($property->fresh(), $today, $dueDay),
            'Should be up to date when every required month has an approved payment'
        );

        // Rompemos el primer período (lo pasamos a PENDING)
        $missing = $expectedPeriods->first();

        Payment::query()
            ->where('property_id', $property->id)
            ->whereDate('period', $missing)
            ->update(['status' => Payment::STATUS_PENDING]);

        // Sanity check: aseguramos que realmente se actualizó (si no, el assertFalse es engañoso)
        $this->assertTrue(
            Payment::query()
                ->where('property_id', $property->id)
                ->whereDate('period', $missing)
                ->where('status', Payment::STATUS_PENDING)
                ->exists(),
            "Expected period {$missing} to be updated to PENDING"
        );

        $this->assertFalse(
            $this->service->isUpToDate($property->fresh(), $today, $dueDay),
            'Missing approved months should mark property as not up to date'
        );
    }

    public function test_next_payable_period_points_to_first_missing_month(): void
    {
        $dueDay = 5;

        $property = $this->propertyRegisteredAt(Carbon::create(2025, 1, 1));
        $today = Carbon::create(2025, 4, 10);

        $start = $this->service->registrationStartPeriod($property)->startOfMonth();
        $end   = $this->service->requiredThroughPeriod($today, $dueDay)->startOfMonth();
        $expected = $this->service->expectedPeriods($start, $end);

        // Pagamos 1ro y 3ro, falta el 2do => nextPayable debe ser el 2do
        $this->createApprovedPayment($property, $expected[0]);
        $this->createApprovedPayment($property, $expected[2]);

        $next = $this->service->nextPayablePeriod($property->fresh(), $today, $dueDay);

        $this->assertSame($expected[1], $next->toDateString());
    }

    private function propertyRegisteredAt(Carbon $date): Property
    {
        $property = Property::factory()->create([
            'active' => true,
            'is_beneficiary' => true,
        ]);

        Property::query()->whereKey($property->id)->update([
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        return $property->fresh();
    }

    private function createApprovedPayment(Property $property, string $period): Payment
    {
        return Payment::query()->create([
            'neighborhood_id' => $property->neighborhood_id,
            'property_id'     => $property->id,
            'period'          => $period, // YYYY-MM-01
            'method'          => Payment::METHOD_CASH,
            'status'          => Payment::STATUS_APPROVED,
            'amount'          => 1000.00,
            'paid_at'         => Carbon::parse($period)->addDay(),
        ]);
    }
}
