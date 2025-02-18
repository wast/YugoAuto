<?php

declare(strict_types=1);

namespace App\Source\Ride\Infra\SearchRides\Services;

use App\Models\Ride;
use App\Source\Ride\Enum\RideExtraFiltersEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class SearchRidesService
{
    public function search(
        int $fromPlaceId,
        int $toPlaceId,
        ?Carbon $minStartTime,
        ?Carbon $maxStartTime,
        string $filter
    ): LengthAwarePaginator {
        //TODO - check query index
        $rides = Ride::where('from_place_id', $fromPlaceId)
            ->where('to_place_id', $toPlaceId)
            ->with([
                'fromPlace',
                'toPlace',
                'driver',
                'rideRequestForAuthUser',
                'acceptedRideRequests'
            ]);

        if ($minStartTime) {
            $rides->whereRaw(sprintf('DATE(time) >= "%s"', $minStartTime->format('Y-m-d')));
        }

        if ($maxStartTime) {
            $rides->whereRaw(sprintf('DATE(time) <= "%s"', $maxStartTime->format('Y-m-d')));
        }

        $rides = match ($filter) {
            RideExtraFiltersEnum::PRICE_LOWEST->value => $rides->orderBy('price', 'ASC'),
            RideExtraFiltersEnum::PRICE_HIGHEST->value => $rides->orderBy('price', 'DESC'),
            RideExtraFiltersEnum::TIME_EARLIEST->value => $rides->orderBy('time', 'ASC'),
            RideExtraFiltersEnum::TIME_LATEST->value => $rides->orderBy('time', 'DESC'),
            default => $rides->orderBy('time', 'ASC')
        };

        return $rides->paginate();
    }

    public function latestRides(): Collection
    {
        return Ride::orderBy('time', 'ASC')
            ->with([
                'fromPlace',
                'toPlace',
                'driver',
                'rideRequestForAuthUser',
                'acceptedRideRequests'
            ])
            ->where('time', '>=', Carbon::now()->format('Y-m-d H:i:s'))
            ->limit(20)
            ->get();
    }
}
