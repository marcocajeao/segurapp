<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use App\Services\Payments\PaymentStatusService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GuardCheckTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_guard_check_requires_authentication(): void
    {
        $property = Property::factory()->create([
            'active' => true,
            'is_beneficiary' => true,
        ]);

        $this->getJson("/api/guard/check/{$property->qr_token}")
            ->assertUnauthorized();
    }

    public function test_guard_check_requires_guard_role(): void
    {
        $user = User::factory()->for(\App\Models\Neighborhood::factory())->create();
        Sanctum::actingAs($user);

        $property = Property::factory()->create([
            'active' => true,
            'is_beneficiary' => true,
        ]);

        $this->getJson("/api/guard/check/{$property->qr_token}")
            ->assertForbidden();
    }

    public function test_guard_receives_paid_status_when_property_is_up_to_date(): void
    {
        $guard = $this->makeGuardUser();

        $property = $this->propertyForGuard($guard, Carbon::create(2025, 1, 1), [
            'active' => true,
            'is_beneficiary' => true,
        ]);

        Carbon::setTestNow(Carbon::create(2025, 2, 10)); // exige Feb (due=5)

        $service = $this->app->make(PaymentStatusService::class);
        $dueDay = $property->neighborhood->default_due_day ?? 5;

        $start = $service->registrationStartPeriod($property)->startOfMonth();
        $end   = $service->requiredThroughPeriod(Carbon::now(), $dueDay)->startOfMonth();
        $expectedPeriods = $service->expectedPeriods($start, $end);

        foreach ($expectedPeriods as $period) {
            $this->createApprovedPayment($property, $period);
        }

        Sanctum::actingAs($guard);

        $response = $this->getJson("/api/guard/check/{$property->qr_token}");

        $response->assertOk();

        // Assert robusto: funciona si el root es objeto o array
        $response->assertJsonFragment([
            'result' => 'PAID',
            'label'  => 'PAGADO',
        ]);

        $response->assertJsonFragment([
            'id' => $property->id,
            'code' => $property->code,
        ]);

        $this->assertDatabaseHas('guard_checks', [
            'guard_id' => $guard->id,
            'property_id' => $property->id,
            'result' => 'PAID',
        ]);
    }

    public function test_guard_receives_unpaid_status_when_property_has_missing_payments(): void
    {
        $guard = $this->makeGuardUser();

        $property = $this->propertyForGuard($guard, Carbon::create(2025, 1, 1), [
            'active' => true,
            'is_beneficiary' => true,
        ]);

        Carbon::setTestNow(Carbon::create(2025, 3, 7)); // exige Mar (due=5)

        // Solo Enero aprobado => faltan Feb y Mar
        $this->createApprovedPayment($property, '2025-01-01');

        Sanctum::actingAs($guard);

        $response = $this->getJson("/api/guard/check/{$property->qr_token}");

        $response->assertOk()
            ->assertJsonFragment([
                'result' => 'UNPAID',
                'label' => 'DEUDOR',
            ]);

        $this->assertDatabaseHas('guard_checks', [
            'guard_id' => $guard->id,
            'property_id' => $property->id,
            'result' => 'UNPAID',
        ]);
    }

    public function test_guard_receives_non_beneficiary_status_when_property_is_inactive(): void
    {
        $guard = $this->makeGuardUser();

        $property = $this->propertyForGuard($guard, Carbon::create(2025, 1, 1), [
            'active' => false,
            'is_beneficiary' => true,
        ]);

        Sanctum::actingAs($guard);
        Carbon::setTestNow(Carbon::create(2025, 2, 10));

        $response = $this->getJson("/api/guard/check/{$property->qr_token}");

        $response->assertOk()
            ->assertJsonFragment([
                'result' => 'NON_BENEFICIARY',
                'label'  => 'NO BENEFICIARIO',
            ]);
    }

    private function propertyForGuard(User $guard, Carbon $registeredAt, array $attributes = []): Property
    {
        $factory = Property::factory()->for($guard->neighborhood);

        if (! empty($attributes)) {
            $factory = $factory->state($attributes);
        }

        $property = $factory->create();

        Property::query()->whereKey($property->id)->update([
            'created_at' => $registeredAt,
            'updated_at' => $registeredAt,
        ]);

        return $property->fresh();
    }

    private function makeGuardUser(): User
    {
        $neighborhood = \App\Models\Neighborhood::factory()->create(['default_due_day' => 5]);

        $user = User::factory()->for($neighborhood)->create();

        $role = Role::firstOrCreate(
            ['slug' => 'GUARDIA'],
            ['name' => 'Guardia']
        );

        $user->roles()->sync([$role->id]);

        return $user->fresh();
    }

    private function createApprovedPayment(Property $property, string $period): Payment
    {
        return Payment::query()->create([
            'neighborhood_id' => $property->neighborhood_id,
            'property_id' => $property->id,
            'period' => $period,
            'method' => Payment::METHOD_CASH,
            'status' => Payment::STATUS_APPROVED,
            'amount' => 1000.00,
            'paid_at' => Carbon::parse($period)->addDay(),
        ]);
    }
}
