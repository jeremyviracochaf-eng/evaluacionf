<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; //Modelo base de Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory; //Permite usar factories para testing

class Reserva extends Model
{
    //
    use HasFactory; 

    protected $fillable = [
        'user_id',
        'atraccion_id',
        'fecha',
        'hora',
        'estado',
        'comentarios',
    ];

    //Relaciones

    /**
     * Relación: belongsTo
     * 
     * Una reserva PERTENECE a un usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /**
     * Relación: belongsTo
     * 
     * Una reserva PERTENECE a una atracción
     */
    public function atraccion()
    {
        return $this->belongsTo(Atraccion::class, 'atraccion_id');
    }

}
