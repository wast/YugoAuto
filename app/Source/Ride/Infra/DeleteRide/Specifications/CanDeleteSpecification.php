<?php

declare(strict_types=1);

namespace App\Source\Ride\Infra\DeleteRide\Specifications;

use App\Models\Ride;

class CanDeleteSpecification
{
    public function isSatisfied(int $rideId, int $userId): bool
    {
        return Ride::where('driver_id', $userId)
                ->where('id', $rideId)
                ->count() > 0;
    }
}
