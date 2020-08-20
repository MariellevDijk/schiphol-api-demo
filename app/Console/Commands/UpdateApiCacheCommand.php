<?php


namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class UpdateApiCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:cache:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the API cache';

    public function handle(): void
    {
        $this->getAirportData();
        $this->getAirlineData();
        $this->getFlightData();
    }

    /**
     * Gets the Airport data from the API
     */
    public function getAirportData(): void
    {
        // @TODO: Put this URL in .env
        $response = Http::get('http://flightassets.datasavannah.com/test/airports.json');

        if (!$response->successful()) {
            return;
        }

        $airports = $response->json();
        Cache::put('airports', $airports, new DateTime('tomorrow'));
    }

    /**
     * Gets the Airline data from the API
     */
    public function getAirlineData(): void
    {
        // @TODO: Put this URL in .env
        $response = Http::get('http://flightassets.datasavannah.com/test/airlines.json');

        if (!$response->successful()) {
            return;
        }

        $airlines = $response->json();
        Cache::put('airlines', $airlines, new DateTime('tomorrow'));
    }

    /**
     * Gets the Flight data from the API
     */
    public function getFlightData(): void
    {
        // @TODO: Put this URL in .env
        $response = Http::get('http://flightassets.datasavannah.com/test/flights.json');

        if (!$response->successful()) {
            return;
        }

        $flights = $response->json();
        Cache::put('flights', $flights, new DateTime('tomorrow'));
    }
}
