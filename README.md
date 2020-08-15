# Schiphol API Demo

Small webservice built with [Lumen](https://lumen.laravel.com/docs) that wraps the Schiphol API and adds some functionality like distance calculation and filtering.

## Installation and Requirements

To install this project, clone this repository and run `composer install`.

### System Requirements

- PHP 7.1 or higher
- Composer
- An active internet connection

## Running the application
For development and testing purposes, the default PHP development server is sufficient.

```bash
$ php -S localhost:8080 -t public
```

## Using the API
The API has 4 endpoints that may be used without authentication or any special configuration. Responses will usually be JSON formatted, except when an error occurs. A good way to check for errors is the HTTP status code. In normal cases this should be `200 OK`. When something goes wrong, the appropriate HTTP status code and message will be returned.

- `/flights` returns a JSON formatted list of flights from the AMS Airport ( Amsterdam Schiphol ) containing the airline ID, flight number, departure airport ID (this should always be `"AMS"`) and arrival airport ID.
    - Example: `{"airlineId":"PK","flightNumber":763,"departureAirportId":"AMS","arrivalAirportId":"FRA"}`
    - Optionally: Use `?airlineId={airlineId}` to see all flights from one airline
- `/airlines` returns a JSON formatted list of airlines with their distance flown today. 
    - Example: `{"id":"KL","name":"KLM","distance":516991.93944829697}`
    - Optionally: Use `?distanceUnit={km|mi}` to select the type of unit the flown distance needs to be.
- `/airports` returns a JSON formatted list of airports, sorted ascending on the distance from AMS Airport ( Amsterdam Schiphol )
    - Example: `{"id":"LHR","name":"Heathrow Airport","distance":370.316816493085}`
    - Optionally: Use  `?distanceUnit={km|mi}` to select the type of unit the distance needs to be.
- `/airport/{id}` returns a JSON formatted list based on the Airport ID, like `"LHR"`, with latitude and longitude of the airport and the country it's based in.
    - Example: `{"id":"LHR","latitude":51.469604,"longitude":-0.453566,"name":"Heathrow Airport","city":"London","countryId":"GB"}`
    
## Caching
Keep in mind, Schiphol API Requests are refreshed and cached until midnight. For the cache to work properly, ensure that the folder `storage/framework/cache/data` is writeable.
