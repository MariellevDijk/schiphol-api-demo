<?php

namespace App\Services;

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
    private $geotools;

    /**
     * AirportService constructor.
     * @param Geotools $geotools
     */
    public function __construct(Geotools $geotools)
    {
        $this->geotools = $geotools;
    }

    /**
     * @return array|mixed
     */
    public function getAirports()
    {
        if ($this->airports === null) {
            $response = Http::get('http://flightassets.datasavannah.com/test/airports.json');
            $this->airports = $response->json();
        }

        return $this->airports;
    }

    /**
     * @return array
     */
    public function getSortedAirports()
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
     * @return array
     */
    public function getAirportDistance()
    {
        $airports = $this->getAirports();
        $airportsSortedByDistance = [];

        foreach ($airports as $airport) {
            $distance = $this->calculateDistance($airport);
            $airportsSortedByDistance[] = [
                'id' => $airport['id'],
                'name' => $airport['name'],
                'distance' => $distance,
            ];
        }

        usort($airportsSortedByDistance, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return $airportsSortedByDistance;
    }

    /**
     * @param $airportId
     * @return array
     */
    public function getLatLong($airportId)
    {
        $airport = $this->getSortedAirports()[$airportId];

        return [
            'latitude' => $airport['latitude'],
            'longitude' => $airport['longitude'],
        ];
    }

    /**
     * @param $airport
     * @return float|int
     */
    public function calculateDistance($airport)
    {
        $airports = $this->getSortedAirports();

        $start = $airports['AMS'];
        $end = $airport;

        $coordA = new Coordinate([$start['latitude'], $start['longitude']]);
        $coordB = new Coordinate([$end['latitude'], $end['longitude']]);
        $distance = $this->geotools->distance()->setFrom($coordA)->setTo($coordB);

        return $distance->in('km')->haversine();
    }
}
