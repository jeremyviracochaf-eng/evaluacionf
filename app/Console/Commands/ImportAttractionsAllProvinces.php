<?php

namespace App\Console\Commands;

use App\Models\Atraccion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportAttractionsAllProvinces extends Command
{
    protected $signature = 'import:all-provinces';
    protected $description = 'Importa atracciones de todas las provincias de Ecuador desde Google Places';

    public function handle()
    {
        $provinces = [
            ['name' => 'Pichincha', 'lat' => -0.2299, 'lon' => -78.5045],
            ['name' => 'Guayas', 'lat' => -2.1900, 'lon' => -79.8852],
            ['name' => 'Azuay', 'lat' => -2.8906, 'lon' => -78.9902],
            ['name' => 'Manabí', 'lat' => -0.9557, 'lon' => -80.7322],
            ['name' => 'Los Ríos', 'lat' => -1.0682, 'lon' => -79.2440],
            ['name' => 'Tungurahua', 'lat' => -1.2290, 'lon' => -78.6272],
            ['name' => 'Imbabura', 'lat' => 0.3520, 'lon' => -78.1200],
            ['name' => 'Cotopaxi', 'lat' => -0.9322, 'lon' => -78.1137],
            ['name' => 'Morona Santiago', 'lat' => -2.2985, 'lon' => -78.1829],
            ['name' => 'Pastaza', 'lat' => -1.5319, 'lon' => -77.9789],
            ['name' => 'Napo', 'lat' => -0.5050, 'lon' => -77.4829],
            ['name' => 'Sucumbíos', 'lat' => 0.0902, 'lon' => -76.8628],
            ['name' => 'Orellana', 'lat' => -0.4620, 'lon' => -76.9829],
            ['name' => 'Santa Elena', 'lat' => -2.2139, 'lon' => -80.3734],
            ['name' => 'El Oro', 'lat' => -3.3682, 'lon' => -79.5938],
            ['name' => 'Loja', 'lat' => -3.9939, 'lon' => -79.2040],
            ['name' => 'Zamora Chinchipe', 'lat' => -4.0608, 'lon' => -78.9779],
            ['name' => 'Chimborazo', 'lat' => -1.6705, 'lon' => -78.6477],
            ['name' => 'Cañar', 'lat' => -2.5586, 'lon' => -78.6140],
            ['name' => 'Esmeraldas', 'lat' => 0.9568, 'lon' => -79.6536],
            ['name' => 'Carchi', 'lat' => 0.5807, 'lon' => -77.2260],
            ['name' => 'Bolívar', 'lat' => -1.4089, 'lon' => -78.8928],
            ['name' => 'Galápagos', 'lat' => -0.9430, 'lon' => -90.5455],
        ];

        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('  Importando Atracciones de Todas las Provincias de Ecuador');
        $this->info('═══════════════════════════════════════════════════════════════');

        $totalImported = 0;

        foreach ($provinces as $province) {
            $this->line("\n🔍 Importando de {$province['name']}...");
            
            try {
                $response = Http::get(
                    'https://maps.googleapis.com/maps/api/place/nearbysearch/json',
                    [
                        'location' => "{$province['lat']},{$province['lon']}",
                        'radius'   => 50000,
                        'type'     => 'tourist_attraction',
                        'key'      => config('services.google.places_key'),
                    ]
                );

                $data = $response->json();
                
                if (!isset($data['results']) || empty($data['results'])) {
                    $this->warn('⚠️  No se encontraron resultados');
                    continue;
                }

                $count = 0;
                foreach ($data['results'] as $place) {
                    $photoRef = $place['photos'][0]['photo_reference'] ?? null;

                    Atraccion::updateOrCreate(
                        ['google_place_id' => $place['place_id']],
                        [
                            'nombre'      => $place['name'],
                            'descripcion' => $place['vicinity'] ?? 'Sin descripción',
                            'categoria'   => implode(',', $place['types'] ?? []),
                            'ubicacion'   => $place['vicinity'] ?? null,
                            'provincia'   => $province['name'],
                            'precio'      => null,
                            'imagen_url'  => $photoRef
                                ? "https://maps.googleapis.com/maps/api/place/photo?maxwidth=800&photo_reference={$photoRef}&key=" . config('services.google.places_key')
                                : null,
                        ]
                    );
                    $count++;
                }
                
                $this->info("✓ {$count} atracciones agregadas/actualizadas");
                $totalImported += $count;

            } catch (\Exception $e) {
                $this->error("❌ Error en {$province['name']}: " . $e->getMessage());
            }

            // Esperar un poco para no sobrecargar Google Places API
            sleep(1);
        }

        $this->info("\n═══════════════════════════════════════════════════════════════");
        $this->line("✓ ¡Total: $totalImported atracciones importadas!");
        $this->info('═══════════════════════════════════════════════════════════════');
    }
}
