<?php

declare(strict_types=1);

namespace App\Source\Ride\App\Controllers;

use App\Enum\TimeEnum;
use App\Http\Controllers\Controller;
use App\Source\Place\Domain\SearchPlaces\SearchPlacesLogic;
use App\Source\Ride\App\Requests\CreateRideRequest;
use App\Source\Ride\App\Requests\SearchRidesRequest;
use App\Source\Ride\Domain\CreateRide\CreateRideLogic;
use App\Source\Ride\Domain\DeleteRide\DeleteRideLogic;
use App\Source\Ride\Domain\MyRides\MyRidesLogic;
use App\Source\Ride\Domain\SearchRides\SearchRidesLogic;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RideController extends Controller
{
    public function search(
        SearchRidesRequest $request,
        SearchRidesLogic $ogic,
        SearchPlacesLogic $placesBusinessLogic
    ) {
        $rides = null;
        $fromPlace = $toPlace = $time = null;

        if ($request->from_place_id && $request->to_place_id && $request->time) {
            $rides = $ogic->search(
                (int)$request->from_place_id,
                (int)$request->to_place_id,
                Carbon::createFromFormat(TimeEnum::TIME_FORMAT->value, $request->time)
            );

            $fromPlace = $placesBusinessLogic->getById((int)$request->from_place_id);
            $toPlace = $placesBusinessLogic->getById((int)$request->to_place_id);
            $time = $request->time;
        }

        return view(
            (Auth::guest()) ? 'ride.search.public_list' : 'ride.search.private_list',
            [
                'rides' => $rides,
                'fromPlace' => $fromPlace,
                'toPlace' => $toPlace,
                'time' => $time,
            ]
        );
    }

    public function showCreate(
        Request $request,
        SearchPlacesLogic $searchPlacesBusinessLogic
    ) {
        $fromPlaceId = old('from_place_id');
        $toPlaceId = old('to_place_id');
        $toPlace = $fromPlace = null;
        if ($fromPlaceId && $toPlaceId) {
            $fromPlace = $searchPlacesBusinessLogic->getById((int)$fromPlaceId);
            $toPlace = $searchPlacesBusinessLogic->getById((int)$toPlaceId);
        }
        return view(
            'ride.create.form',
            [
                'fromPlace' => $fromPlace,
                'toPlace' => $toPlace,
            ]
        );
    }

    public function save(
        CreateRideRequest $request,
        CreateRideLogic $ogic
    ) {
        try {
            $ogic->create(
                Auth::id(),
                (int)$request->from_place_id,
                (int)$request->to_place_id,
                Carbon::createFromFormat(TimeEnum::TIME_FORMAT->value, $request->time),
                (int)$request->number_of_seats,
                (int)$request->price,
                $request->description
            );
        } catch (Exception $exception) {
            $request->session()->flash('error', $exception->getMessage());
            return redirect()->back()->withInput();
        }
        return redirect(route('ride.my-rides'));
    }

    public function myRides(
        MyRidesLogic $ogic
    ) {
        $authUserId = Auth::id();
        $rides = $ogic->get($authUserId);
        return view(
            'ride.my-rides.list',
            compact('rides')
        );
    }

    public function delete(
        int $id,
        Request $request,
        DeleteRideLogic $ogic
    ) {
        try {
            $ogic->delete($id, Auth::id());
        } catch (Exception $exception) {
            $request->session()->flash('error', $exception->getMessage());
            Log::error($exception->getMessage());
        }
        return redirect()->back();
    }
}
