<?php

namespace App\Http\Controllers;

use App\Services\AirlineService;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class AirlineController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param AirlineService $airlineService
     * @param Request $request
     * @return array
     */
    public function list(AirlineService $airlineService, Request $request): array
    {
        $distanceUnit = $request->get('distanceUnit', 'km');

        if (!in_array($distanceUnit, ['km', 'mi'])) {
            abort(400, 'Invalid distance Unit');
        }

        return $airlineService->getSortedAirlines($distanceUnit);
    }
}
