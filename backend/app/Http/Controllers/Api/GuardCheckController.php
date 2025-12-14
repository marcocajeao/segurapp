<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Guard\GuardCheckService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuardCheckController extends Controller
{
    public function __construct(
        private readonly GuardCheckService $guardCheckService
    ) {}

    public function check(Request $request, string $qrToken): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $payload = $this->guardCheckService->checkByQrToken(
            qrToken: $qrToken,
            guard: $user,
            today: Carbon::now()
        );

        return response()->json($payload);
    }
}
