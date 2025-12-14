#!/usr/bin/env php
<?php
/**
 * Script para importar atracciones de todas las provincias de Ecuador desde Google Places
 * 
 * Coordenadas de las ciudades principales de cada provincia
 */

$provinces = [
    ['name' => 'Pichincha', 'lat' => -0.2299, 'lon' => -78.5045],      // Quito
    ['name' => 'Guayas', 'lat' => -2.1900, 'lon' => -79.8852],         // Guayaquil
    ['name' => 'Azuay', 'lat' => -2.8906, 'lon' => -78.9902],          // Cuenca
    ['name' => 'Manabí', 'lat' => -0.9557, 'lon' => -80.7322],         // Manta
    ['name' => 'Los Ríos', 'lat' => -1.0682, 'lon' => -79.2440],       // Babahoyo
    ['name' => 'Tungurahua', 'lat' => -1.2290, 'lon' => -78.6272],     // Ambato
    ['name' => 'Imbabura', 'lat' => 0.3520, 'lon' => -78.1200],        // Ibarra
    ['name' => 'Cotopaxi', 'lat' => -0.9322, 'lon' => -78.1137],       // Latacunga
    ['name' => 'Morona Santiago', 'lat' => -2.2985, 'lon' => -78.1829],// Macas
    ['name' => 'Pastaza', 'lat' => -1.5319, 'lon' => -77.9789],        // Puyo
    ['name' => 'Napo', 'lat' => -0.5050, 'lon' => -77.4829],           // Tena
    ['name' => 'Sucumbíos', 'lat' => 0.0902, 'lon' => -76.8628],       // Nueva Loja
    ['name' => 'Orellana', 'lat' => -0.4620, 'lon' => -76.9829],       // Francisco de Orellana
    ['name' => 'Santa Elena', 'lat' => -2.2139, 'lon' => -80.3734],    // Santa Elena
    ['name' => 'El Oro', 'lat' => -3.3682, 'lon' => -79.5938],         // Machala
    ['name' => 'Loja', 'lat' => -3.9939, 'lon' => -79.2040],           // Loja
    ['name' => 'Chimborazo', 'lat' => -1.6705, 'lon' => -78.6477],     // Riobamba
    ['name' => 'Cañar', 'lat' => -2.5586, 'lon' => -78.6140],          // Azogues
    ['name' => 'Esmeraldas', 'lat' => 0.9568, 'lon' => -79.6536],      // Esmeraldas
    ['name' => 'Carchi', 'lat' => 0.5807, 'lon' => -77.2260],          // Tulcán
    ['name' => 'Bolívar', 'lat' => -1.4089, 'lon' => -78.8928],        // Guaranda
];

echo "═══════════════════════════════════════════════════════════════\n";
echo "  Importador de Atracciones de Google Places - Ecuador\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "INSTRUCCIONES:\n";
echo "1. Abre Postman o cualquier cliente HTTP\n";
echo "2. Haz POST a: http://127.0.0.1:8000/api/atracciones/import-google\n";
echo "3. En Body (raw JSON), copia y pega las siguientes llamadas:\n\n";

echo "IMPORTANTE: Después de cada importación, edita las atracciones en el admin\n";
echo "para asignar la provincia correcta (actualmente todas dicen 'Pichincha')\n\n";

$count = 1;
foreach ($provinces as $province) {
    $json = json_encode([
        'lat' => $province['lat'],
        'lon' => $province['lon'],
        'radius' => 50000
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
    echo "╔═ PROVINCIA #$count: {$province['name']} ════════════════════════════════════════╗\n";
    echo "║ Coordenadas: {$province['lat']}, {$province['lon']}\n";
    echo "║ Copia este JSON en el Body de Postman:\n";
    echo "╠════════════════════════════════════════════════════════════════════════════════╣\n";
    echo "$json\n";
    echo "╚════════════════════════════════════════════════════════════════════════════════╝\n\n";
    
    $count++;
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "⚠️  ALTERNATIVA: Script PHP Automático\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Si quieres hacerlo automático, ejecuta:\n\n";
echo "php artisan tinker\n";
echo ">> include('import_all_provinces.php')\n\n";

?>
