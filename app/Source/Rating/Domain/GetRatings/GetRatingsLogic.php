<?php

declare(strict_types=1);

namespace App\Source\Rating\Domain\GetRatings;

use App\Source\Rating\Infra\GetRatings\Services\GetRatingsService;
use App\Source\RideRequest\Enum\RideRequestEnum;
use App\Source\RideRequest\Infra\Common\Specifications\CanAccessRideSpecification;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class GetRatingsLogic
{
    public function __construct(
        private readonly CanAccessRideSpecification $canAccessRideSpecification,
        private readonly GetRatingsService $getRatingsService
    ) {
    }

    public function getRatings(int $userId, int $rideId): Collection
    {
        $canAccess = $this->canAccessRideSpecification->isSatisfiedByDriverOrPassenger(
            userId: $userId,
            rideId: $rideId,
            status: RideRequestEnum::ACCEPTED
        );
        if (!$canAccess) {
            throw new Exception(__('You cannot access this page'));
        }

        return $this->getRatingsService->get($rideId);
    }
}
