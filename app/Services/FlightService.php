<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Class FlightService
 * @package App\Services
 */
class FlightService
{
    /**
     * @var array|mixed
     */
    private $flights;

    /**
     * @var array
     */
    private $flightsByAirline = [];

    /**
     * @var AirportService
     */
    private $airportService;

    /**
     * FlightService constructor.
     * @param AirportService $airportService
     */
    public function __construct(AirportService $airportService)
    {
        $this->airportService = $airportService;
    }

    /**
     * @return array|mixed
     */
    public function getFlights()
    {
        if (!Cache::has('flights') && $this->flights === null) {
            $response = Http::get('http://flightassets.datasavannah.com/test/flights.json');

            if (!$response->successful()) {
                abort(503, '503 - Service Unavailable');
            }
            $this->flights = $response->json();
            Cache::put('flights', $this->flights, new \DateTime('tomorrow'));
        } else if (Cache::has('flights')){
            $this->flights = Cache::get('flights');
        }

        return $this->flights;
    }

    /**
     * @return array
     */
    public function getFlightsByAirline(): array
    {
        $flights = $this->getFlights();

        foreach ($flights as $flight) {
            $this->flightsByAirline[$flight['airlineId']][$flight['flightNumber']] = $flight;
        }

        return $this->flightsByAirline;
    }

    /**
     * @param string $distanceUnit
     * @return array
     */
    public function getDistanceFlownByAirline(string $distanceUnit): array
    {
        $flightsByAirline = $this->getFlightsByAirline();

        $distanceByAirline = [];

        foreach ($flightsByAirline as $airline) {
            foreach ($airline as $flight) {
                $latlong = $this->airportService->getLatLong($flight['arrivalAirportId']);

                $distance = $this->airportService->calculateDistance($latlong, $distanceUnit);

                if (!array_key_exists($flight['airlineId'], $distanceByAirline)) {
                    $distanceByAirline[$flight['airlineId']] = 0;
                }
                $distanceByAirline[$flight['airlineId']] += $distance;
            }
        }

        return $distanceByAirline;
    }

    /**
     * @param string $airlineId
     * @return array|null
     */
    public function getFlightsByAirlineId(string $airlineId): ?array
    {
        return $this->getFlightsByAirline()[$airlineId] ?? null;
    }
}
