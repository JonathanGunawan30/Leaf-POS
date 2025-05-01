<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class DistanceHelper
{
    public static function getDistanceInKm(string $origin, string $destination): ?float
    {
        $apiKey = config('services.google_maps.key');

        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $origin,
            'destinations' => $destination,
            'mode' => 'driving',
            'key' => $apiKey,
        ]);

        if ($response->ok() && $response['rows'][0]['elements'][0]['status'] === 'OK') {
            $meters = $response['rows'][0]['elements'][0]['distance']['value'];
            return round($meters / 1000, 2);
        }

        return null;
    }

    public static function calculateShippingCost(string $origin, string $destination): ?int
    {
        $distanceKm = self::getDistanceInKm($origin, $destination);

        if ($distanceKm === null) {
            return null;
        }

        $costPerKm = 2000;
        return (int) ceil($distanceKm * $costPerKm);
    }
}
