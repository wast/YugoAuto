<?php

namespace App\Source\RideRequest\Domain\RideRequests;

use App\Models\Ride;
use App\Source\RideRequest\Infra\Common\Specifications\CanAccessRideSpecification;
use App\Source\RideRequest\Infra\RideRequests\Services\GetRideRequestsService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RideRequestsLogic
{
    public function __construct(
        private CanAccessRideSpecification $canDriverAccessRideSpecification,
        private GetRideRequestsService $getRideRequestsService
    ) {
    }

    public function getRequests(int $userId, int $rideId): LengthAwarePaginator
    {
        if (!$this->canDriverAccessRideSpecification->isSatisfiedByDriver($userId, $rideId)) {
            throw new Exception('Cannot access ride');
        }

        return $this->getRideRequestsService->get($rideId);
    }

    public function getRide(int $rideId): Ride
    {
        return Ride::find($rideId);
    }
}
