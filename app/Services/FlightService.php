<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

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
        return $this->flights = Cache::get('flights');
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
     * @param string $airlineId
     * @return array|null
     */
    public function getFlightsByAirlineId(string $airlineId): ?array
    {
        return $this->getFlightsByAirline()[$airlineId] ?? null;
    }
}
