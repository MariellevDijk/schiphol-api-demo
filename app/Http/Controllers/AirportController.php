<?php

namespace App\Http\Controllers;

use App\Services\AirportService;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class AirportController extends Controller
{
    /**
     * @var AirportService
     */
    private $airportService;

    /**
     * AirportController constructor.
     * @param AirportService $airportService
     */
    public function __construct(AirportService $airportService)
    {
        $this->airportService = $airportService;
    }

    /**
     * Create a new controller instance.
     *
     * @param Request $request
     * @return array
     */
    public function list(Request $request): array
    {
        $distanceUnit = $request->get('distanceUnit', 'km');

        if (!in_array($distanceUnit, ['km', 'mi'])) {
            abort(404, '404 - Invalid distance Unit');
        }

        return $this->airportService->getAirportDistance($distanceUnit);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getAirport(string $id): array
    {
        $airports = $this->airportService->getSortedAirports();
        $id = strtoupper($id);

        if (!array_key_exists($id, $airports)) {
            abort(400, '400 - Invalid Airport ID');
        }

        return $airports[$id];
    }
}
