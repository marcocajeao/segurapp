<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\Property;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class PaymentStatusService
{
    public function requiredThroughPeriod(Carbon $today, int $dueDay): Carbon
    {
        $monthStart = $today->copy()->startOfMonth();

        // Deudor al día siguiente del vencimiento:
        // si hoy >= dueDay + 1 => exige mes actual; sino exige mes anterior.
        return $today->day >= ($dueDay + 1)
            ? $monthStart
            : $monthStart->copy()->subMonth();
    }

    public function registrationStartPeriod(Property $property): Carbon
    {
        $registeredAt = $property->registered_at ?? $property->created_at;

        return Carbon::parse($registeredAt)->startOfMonth();
    }

    /**
     * Devuelve true si la propiedad tiene TODOS los meses pagos
     * desde el mes de registro hasta el período requerido.
     */
    public function isUpToDate(Property $property, Carbon $today, int $dueDay): bool
    {
        if (! $property->isActiveBeneficiary()) {
            return false;
        }

        $start = $this->registrationStartPeriod($property);
        $end   = $this->requiredThroughPeriod($today, $dueDay)->startOfMonth();

        if ($end->lt($start)) {
            // Se registró este mes y aún no venció, no debe nada todavía
            return true;
        }

        $paidPeriods = $this->approvedPeriodsInRange($property, $start, $end);
        $expected    = $this->expectedPeriods($start, $end);

        return $expected->diff($paidPeriods)->isEmpty();
    }

    public function approvedPeriodsInRange(Property $property, Carbon $start, Carbon $end): Collection
    {
        return Payment::query()
            ->where('property_id', $property->id)
            ->where('status', Payment::STATUS_APPROVED)
            // Usar whereDate evita problemas de comparación si el driver/cast juega con timezones o tipos
            ->whereDate('period', '>=', $start->toDateString())
            ->whereDate('period', '<=', $end->toDateString())
            ->pluck('period')
            ->map(fn ($d) => Carbon::parse($d)->startOfMonth()->toDateString())
            ->unique()
            ->values();
    }

    public function expectedPeriods(Carbon $start, Carbon $end): Collection
    {
        return collect(CarbonPeriod::create($start, '1 month', $end))
            ->map(fn (Carbon $d) => $d->startOfMonth()->toDateString())
            ->values();
    }

    /**
     * El próximo período que se permite pagar (secuencial).
     * Si ya está al día => devuelve el mes siguiente al último requerido.
     */
    public function nextPayablePeriod(Property $property, Carbon $today, int $dueDay): Carbon
    {
        $start = $this->registrationStartPeriod($property);
        $end   = $this->requiredThroughPeriod($today, $dueDay)->startOfMonth();

        if ($end->lt($start)) {
            // Todavía no debe, pero igual si quiere pagar "adelantado" NO lo permitimos por tu regla.
            return $start;
        }

        $paid = $this->approvedPeriodsInRange($property, $start, $end)->all();

        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            if (! in_array($key, $paid, true)) {
                return $cursor;
            }
            $cursor->addMonth();
        }

        // Si todo está pago hasta end, próximo pagable es end+1,
        // pero por tu regla no dejamos pagar futuros si no corresponde;
        // esto lo decide el flujo de pagos.
        return $end->copy()->addMonth();
    }
}
