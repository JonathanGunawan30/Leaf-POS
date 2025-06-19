<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Exceptions\AddressNotFound;
use App\Exceptions\DistanceNotFound;

class ShippingService
{
    /**
     * Geocodes an address to latitude and longitude coordinates.
     *
     * @param string $address
     * @return array ['lat', 'lon']
     * @throws AddressNotFound
     */
    private function geocode(string $address): array
    {
        $encodedAddress = urlencode($address);
        $response = Http::withHeaders([
            "User-Agent" => "MyAppName/1.0 (contact@myapp.com)"
        ])->get("https://nominatim.openstreetmap.org/search?q={$encodedAddress}&format=json");

        $data = $response->json();

        if (isset($data[0])) {
            $lat = $data[0]['lat'];
            $lon = $data[0]['lon'];
            return ['lat' => $lat, 'lon' => $lon];
        } else {
            throw new AddressNotFound('Address not found for geocoding: ' . $address);
        }
    }

    /**
     * Calculates the driving distance between two sets of coordinates using OSRM.
     *
     * @param array $originCoords ['lat', 'lon']
     * @param array $destinationCoords ['lat', 'lon']
     * @return float Distance in kilometers
     * @throws DistanceNotFound
     */
    private function calculateDistance(array $originCoords, array $destinationCoords): float
    {
        $originLat = $originCoords['lat'];
        $originLon = $originCoords['lon'];
        $destinationLat = $destinationCoords['lat'];
        $destinationLon = $destinationCoords['lon'];

        $url = "http://router.project-osrm.org/route/v1/driving/{$originLon},{$originLat};{$destinationLon},{$destinationLat}?overview=false";
        $response = Http::get($url);
        $data = $response->json();

        if (isset($data['routes'][0]['legs'][0]['distance'])) {
            return $data['routes'][0]['legs'][0]['distance'] / 1000;
        } else {
            throw new DistanceNotFound('Distance not found between coordinates.');
        }
    }

    /**
     * Calculates shipping details (distance and cost) between two addresses.
     *
     * @param string $originAddress
     * @param string $destinationAddress
     * @return array ['distance_km', 'shipping_cost']
     * @throws AddressNotFound|DistanceNotFound|\Exception
     */
    public function getShippingDetails(string $originAddress, string $destinationAddress): array
    {
        $originCoords = $this->geocode($originAddress);
        $destinationCoords = $this->geocode($destinationAddress);

        $distance = $this->calculateDistance($originCoords, $destinationCoords);
        $shippingCost = $distance * 5000;

        return [
            'distance_km' => round($distance, 2),
            'shipping_cost' => round($shippingCost, 0)
        ];
    }
}
