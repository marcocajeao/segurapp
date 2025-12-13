<?php

namespace App\Services\Tenancy;

use App\Models\User;
use App\Models\Neighborhood;

class TenantService
{
    public function getUserNeighborhood(User $user): Neighborhood
    {
        $neighborhood = $user->neighborhood;

        if (! $neighborhood) {
            throw new \RuntimeException('User has no neighborhood assigned.');
        }

        if (! $neighborhood->active) {
            throw new \RuntimeException('Neighborhood is not active.');
        }

        return $neighborhood;
    }

    public function neighborhoodId(User $user): int
    {
        return $this->getUserNeighborhood($user)->id;
    }
}
