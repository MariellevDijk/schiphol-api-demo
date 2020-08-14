<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use App\Services\FlightService;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param FlightService $flightService
     * @param Request $request
     * @return array
     */
    public function list(FlightService $flightService, Request $request): array
    {
        if ($request->has('airlineId')) {
            $flights = $flightService->getFlightsByAirlineId($request->get('airlineId'));

            if ($flights === null) {
                abort(400, '400 - Invalid Airline Id');
            }
            return $flights;
        }
        return $flightService->getFlights();
    }
}
