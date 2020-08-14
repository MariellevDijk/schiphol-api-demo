<?php

namespace App\Http\Controllers;

use App\Services\AirportService;

class AirportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return array
     */
    public function list(AirportService $airportService)
    {
        return $airportService->getAirportDistance();
    }

    public function getAirport(string $id)
    {
        $airports = $this->getSortedAirports();

        $id = strtoupper($id);

        if (!array_key_exists($id, $airports)) {
            abort(404, 'Invalid Airport ID');
        }

        return $airports[$id];
    }
}
