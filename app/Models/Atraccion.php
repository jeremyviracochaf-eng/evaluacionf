<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Reserva;

/**
 * Atraccion Model
 * 
 * Representa la tabla 'atracciones' en la BD
 * 
 * Campos:
 * - id (PK)
 * - google_place_id (ID externo de Google Places)
 * - nombre (Ej: "Parque Metropolitano")
 * - descripcion (Ej: "Parque grande con vistas...")
 * - categoria (Ej: "Parques", "Museos", "Restaurantes")
 * - ubicacion (Ej: "Quito, Pichincha")
 * - provincia (Ej: "Pichincha") - para filtrado
 * - precio (Ej: 5.00, nullable)
 * - imagen_url (URL de imagen en Firebase o Google)
 * - created_at (Fecha de creación)
 * - updated_at (Última actualización)
 * 
 * Relaciones:
 * - hasMany('reservas') → Una atracción puede tener muchas reservas
 */
class Atraccion extends Model
{
    // Usar factory para testing
    use HasFactory;

    // Especificar nombre de tabla (Laravel asume 'atraccions' pero queremos 'atracciones')
    protected $table = 'atracciones';

    /**
     * $fillable - Campos que pueden asignarse masivamente
     * 
     * Importante de seguridad: Solo listar campos que QUEREMOS que se asignen
     * Esto previene que usuarios asignen campos no autorizados (ej: is_admin)
     * 
     * Usar con: Atraccion::create($data) o $atraccion->update($data)
     * Laravel solo asignará los campos en este array
     */
    protected $fillable = [
        'google_place_id',  // ID de Google Places API
        'nombre',           // Nombre de la atracción
        'descripcion',      // Descripción larga
        'categoria',        // Tipo: Parques, Museos, etc.
        'ubicacion',        // Dirección específica
        'provincia',        // Provincia de Ecuador (para filtrado)
        'precio',           // Precio de entrada (opcional)
        'imagen_url',       // URL de imagen (Firebase o Google)
    ];

    /**
     * Relación: hasMany
     * 
     * Una atracción TIENE MUCHAS reservas
     * 
     * Flujo:
     * 1. Usuario hace reserva de una atracción
     * 2. Se crea registro en tabla 'reservas'
     * 3. Con atraccion_id que apunta a esta atracción
     * 4. Usar $atraccion->reservas() para obtener todas
     * 
     * Ejemplo:
     * $atraccion = Atraccion::find(1);
     * $reservas = $atraccion->reservas;  // Todas las reservas de esta atracción
     */
    public function reservas()
    {
        // Relación uno-a-muchos con modelo Reserva
        // Foreign key: 'atraccion_id' (por defecto atraccion_id)
        // Local key: 'id' (por defecto id)
        return $this->hasMany(Reserva::class);
    }

}
