<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Atraccion extends Model
{
    //
    use HasFactory;

    protected $table = 'atracciones';

    protected $fillable = [
        'google_place_id',
        'nombre',
        'descripcion',
        'categoria',
        'ubicacion',
        'provincia',
        'precio',
        'imagen_url',
    ];

    // Relación: una atracción tiene muchas reservas
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

}
