<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AirlineService
{
    /**
     * @var array
     */
    private $airlines;

    /**
     * @var FlightService
     */
    private $flightService;

    /**
     * AirlineService constructor.
     * @param FlightService $flightService
     */
    public function __construct(FlightService $flightService)
    {
        $this->flightService = $flightService;
    }

    /**
     * @return array
     */
    public function getAirlines(): array
    {
        if (!Cache::has('airlines') && $this->airlines === null) {
            $response = Http::get('http://flightassets.datasavannah.com/test/airlines.json');

            if (!$response->successful()) {
                abort(503, '503 - Service Unavailable');
            }
            $this->airlines = $response->json();
            Cache::put('airlines', $this->airlines, new \DateTime('tomorrow'));
        } else if (Cache::has('airlines')){
            $this->airlines = Cache::get('airlines');
        }

        return $this->airlines;
    }

    /**
     * @param string $distanceUnit
     * @return array
     */
    public function getSortedAirlines(string $distanceUnit): array
    {
        $airlines = $this->getAirlines();

        $distances = $this->flightService->getDistanceFlownByAirline($distanceUnit);

        foreach ($airlines as $airline) {
            $airlinesByDistanceTraveled[] = [
                'id' => $airline['id'],
                'name' => $airline['name'],
                'distance' => $distances[$airline['id']] ?? 0,
            ];
        }

        usort($airlinesByDistanceTraveled, static function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return $airlinesByDistanceTraveled;
    }
}
