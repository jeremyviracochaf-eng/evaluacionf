<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Atraccion extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
        'ubicacion',
        'precio',
        'imagen_url',
    ];


}
