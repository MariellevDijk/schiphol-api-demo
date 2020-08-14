<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Geotools;

/**
 * Class AirportService
 * @package App\Services
 */
class AirportService
{
    /**
     * @var array|mixed
     */
    private $airports;

    /**
     * @var array
     */
    private $sortedAirports = [];

    /**
     * @var Geotools
     */
    private $geoTools;

    /**
     * AirportService constructor.
     * @param Geotools $geoTools
     */
    public function __construct(Geotools $geoTools)
    {
        $this->geoTools = $geoTools;
    }

    /**
     * @return array|mixed
     */
    public function getAirports()
    {
        if (!Cache::has('airports') && $this->airports === null) {
            $response = Http::get('http://flightassets.datasavannah.com/test/airports.json');

            if (!$response->successful()) {
                abort(503, '503 - Service Unavailable');
            }
            $this->airports = $response->json();
            Cache::put('airports', $this->airports, new \DateTime('tomorrow'));
        } else if (Cache::has('airports')) {
            $this->airports = Cache::get('airports');
        }

        return $this->airports;
    }

    /**
     * @return array
     */
    public function getSortedAirports(): array
    {
        if (empty($this->sortedAirports)) {
            $airports = $this->getAirports();
            foreach ($airports as $airport) {
                $this->sortedAirports[$airport['id']] = $airport;
            }
        }

        return $this->sortedAirports;
    }

    /**
     * @param string $distanceUnit
     * @return array
     */
    public function getAirportDistance(string $distanceUnit): array
    {
        $airports = $this->getAirports();
        $airportsSortedByDistance = [];

        foreach ($airports as $airport) {
            $distance = $this->calculateDistance($airport, $distanceUnit);
            $airportsSortedByDistance[] = [
                'id' => $airport['id'],
                'name' => $airport['name'],
                'distance' => $distance,
            ];
        }

        usort($airportsSortedByDistance, static function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return $airportsSortedByDistance;
    }

    /**
     * @param string $airportId
     * @return array
     */
    public function getLatLong(string $airportId): array
    {
        $airport = $this->getSortedAirports()[$airportId];

        return [
            'latitude' => $airport['latitude'],
            'longitude' => $airport['longitude'],
        ];
    }

    /**
     * @param array $airport
     * @param string $distanceUnit
     * @return float|int
     */
    public function calculateDistance(array $airport, string $distanceUnit)
    {
        $airports = $this->getSortedAirports();

        $start = $airports['AMS'];
        $end = $airport;

        $coordA = new Coordinate([$start['latitude'], $start['longitude']]);
        $coordB = new Coordinate([$end['latitude'], $end['longitude']]);
        $distance = $this->geoTools->distance()->setFrom($coordA)->setTo($coordB);

        return $distance->in($distanceUnit)->haversine();
    }
}
