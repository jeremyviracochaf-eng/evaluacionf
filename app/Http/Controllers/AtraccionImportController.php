<?php

namespace App\Http\Controllers;

use App\Models\Atraccion;
use App\Services\GooglePlacesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AtraccionImportController extends Controller
{
    public function importFromGoogle(Request $request, GooglePlacesService $places)
    {
     $lat = $request->lat;
        $lon = $request->lon;
        $radius = $request->radius ?? 30000;

        $response = Http::get(
            'https://maps.googleapis.com/maps/api/place/nearbysearch/json',
            [
                'location' => "$lat,$lon",
                'radius'   => $radius,
                'type'     => 'tourist_attraction',
                'key'      => config('services.google.places_key'),
            ]
        );

        $data = $response->json();

        if (!isset($data['results'])) {
            return response()->json([
                'message' => 'Respuesta inválida de Google',
                'raw' => $data
            ], 500);
        }

        $saved = [];

        foreach ($data['results'] as $place) {

            $photoRef = $place['photos'][0]['photo_reference'] ?? null;

            $atraccion = Atraccion::updateOrCreate(
                ['google_place_id' => $place['place_id']],
                [
                    'nombre'      => $place['name'],
                    'descripcion' => $place['vicinity'] ?? 'Sin descripción',
                    'categoria'   => implode(',', $place['types'] ?? []),
                    'ubicacion'   => $place['vicinity'] ?? null,
                    'precio'      => null,
                    'imagen_url'  => $photoRef
                        ? "https://maps.googleapis.com/maps/api/place/photo?maxwidth=800&photo_reference={$photoRef}&key=" . config('services.google.places_key')
                        : null,
                ]
            );

            $saved[] = $atraccion;
        }

        return response()->json([
            'message' => 'Atracciones importadas desde Google Places',
            'count' => count($saved),
            'atracciones' => $saved,
        ]);
    }
}