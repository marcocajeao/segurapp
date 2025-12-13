<?php

namespace App\Services\Guard;

use App\Models\GuardCheck;
use App\Models\Property;
use App\Models\User;
use App\Services\Payments\PaymentStatusService;
use Carbon\Carbon;

class GuardCheckService
{
    public function __construct(
        private readonly PaymentStatusService $paymentStatusService
    ) {}

    public function checkByQrToken(string $qrToken, User $guard, Carbon $today): array
    {
        $property = Property::query()
            ->where('qr_token', $qrToken)
            ->first();

        if (! $property || ! $property->isActiveBeneficiary()) {
            $this->log($guard, $property?->id, GuardCheck::RESULT_NON_BENEFICIARY);
            return [
                'result' => GuardCheck::RESULT_NON_BENEFICIARY,
                'label'  => 'NO BENEFICIARIO',
            ];
        }

        $dueDay = $property->neighborhood->default_due_day ?? 5;

        $isOk = $this->paymentStatusService->isUpToDate($property, $today, (int) $dueDay);

        $result = $isOk ? GuardCheck::RESULT_PAID : GuardCheck::RESULT_UNPAID;
        $label  = $isOk ? 'PAGADO' : 'DEUDOR';

        $this->log($guard, $property->id, $result);

        return [
            'result' => $result,
            'label'  => $label,
            'property' => [
                'id' => $property->id,
                'street' => $property->street,
                'number' => $property->number,
                'code' => $property->code,
            ],
        ];
    }

    private function log(User $guard, ?int $propertyId, string $result): void
    {
        GuardCheck::create([
            'neighborhood_id' => $guard->neighborhood_id,
            'property_id'     => $propertyId,
            'guard_id'        => $guard->id,
            'result'          => $result,
        ]);
    }
}
