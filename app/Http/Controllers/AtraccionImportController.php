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
        // Validar parámetros de entrada
     $lat = $request->lat;
        $lon = $request->lon;
        $radius = $request->radius ?? 30000;

        // Llamar a la API de Google Places para obtener atracciones turísticas cercanas

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

        // Verificar que la respuesta contiene resultados

        if (!isset($data['results'])) {
            return response()->json([
                'message' => 'Respuesta inválida de Google',
                'raw' => $data
            ], 500);
        }

        // Guardar las atracciones en la base de datos

        $saved = [];

        foreach ($data['results'] as $place) {

            $photoRef = $place['photos'][0]['photo_reference'] ?? null;

            // Usar updateOrCreate para evitar duplicados por google_place_id

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

            // Agregar a la lista de guardadas

            $saved[] = $atraccion;
        }

        // Devolver la respuesta

        return response()->json([
            'message' => 'Atracciones importadas desde Google Places',
            'count' => count($saved),
            'atracciones' => $saved,
        ]);
    }
}