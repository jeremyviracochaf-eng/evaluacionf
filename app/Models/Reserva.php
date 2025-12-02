<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function atraccion()
    {
        return $this->belongsTo(Atraccion::class, 'atraccion_id');
    }

}
