<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

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
     * @var AirportService
     */
    private $airportService;

    /**
     * AirlineService constructor.
     * @param FlightService $flightService
     * @param AirportService $airportService
     */
    public function __construct(FlightService $flightService, AirportService $airportService)
    {
        $this->flightService = $flightService;
        $this->airportService = $airportService;
    }

    /**
     * @return array
     */
    public function getAirlines(): array
    {
        return $this->airlines = Cache::get('airlines');
    }

    /**
     * @param string $distanceUnit
     * @return array
     */
    public function getSortedAirlines(string $distanceUnit): array
    {
        $airlines = $this->getAirlines();

        $distances = $this->getDistanceFlownByAirline($distanceUnit);

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

    /**
     * @param string $distanceUnit
     * @return array
     */
    public function getDistanceFlownByAirline(string $distanceUnit): array
    {
        $flightsByAirline = $this->flightService->getFlightsByAirline();

        $distanceByAirline = [];

        foreach ($flightsByAirline as $airline) {
            foreach ($airline as $flight) {
                $airlineId = $flight['airlineId'];
                $latlong = $this->airportService->getLatLong($flight['arrivalAirportId']);
                $distance = $this->airportService->calculateDistance($latlong, $distanceUnit);

                if (!array_key_exists($airlineId, $distanceByAirline)) {
                    $distanceByAirline[$airlineId] = 0;
                }
                $distanceByAirline[$airlineId] += $distance;
            }
        }

        return $distanceByAirline;
    }
}
