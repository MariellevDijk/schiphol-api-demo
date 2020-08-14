<?php

namespace App\Http\Controllers;

use App\Services\FlightService;

class FlightController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param FlightService $flightService
     * @return array
     */
    public function list(FlightService $flightService)
    {
        return $flightService->getDistanceFlownByAirline();
    }
}
