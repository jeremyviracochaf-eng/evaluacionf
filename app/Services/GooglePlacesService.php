<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GooglePlacesService
{
    protected string $apiKey;

    // Constructor to initialize the API key

    public function __construct()
    {
        $this->apiKey = config('services.google.places_key');
    }

    // Método para buscar lugares cercanos

    public function nearbySearch(float $lat, float $lon, int $radius = 30000, int $limit = 20): array
    {

    $response = Http::get(
        'https://maps.googleapis.com/maps/api/place/nearbysearch/json',
        [
            'location' => $lat . ',' . $lon,
            'radius'   => $radius,
            'type'     => 'tourist_attraction',
            'key'      => $this->apiKey,
        ]
    );

    if ($response->failed()) {
        return [];
    }

    return array_slice(
        $response->json()['results'] ?? [],
        0,
        $limit
    );
    }

    // Método para obtener detalles de un lugar por su place_id

    public function getDetails(string $placeId): array
    {
        $response = Http::get(
            'https://maps.googleapis.com/maps/api/place/details/json',
            [
                'place_id' => $placeId,
                'fields'   => 'name,formatted_address,geometry,types,photos',
                'key'      => $this->apiKey,
            ]
        );

        if ($response->failed() || $response->json('status') !== 'OK') {
            return [];
        }

        return $response->json('result', []);
    }

    // Método para construir la URL de una foto dado su photo_reference

    public function getPhotoUrl(?string $photoReference, int $maxWidth = 600): ?string
    {
        if (!$photoReference) {
            return null;
        }

        return "https://maps.googleapis.com/maps/api/place/photo"
            . "?maxwidth={$maxWidth}"
            . "&photo_reference={$photoReference}"
            . "&key={$this->apiKey}";
    }
}
