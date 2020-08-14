<?php

namespace App\Http\Controllers;

use App\Services\AirlineService;

class AirlineController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param AirlineService $airlineService
     * @return array
     */
    public function list(AirlineService $airlineService)
    {
        return $airlineService->getSortedAirlines();
    }
}
