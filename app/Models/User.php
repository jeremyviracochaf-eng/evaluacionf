<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory; //Permite crear usuarios falsos para pruebas
use Illuminate\Foundation\Auth\User as Authenticatable; //Esto hace que User sea un usuario autenticable
use Illuminate\Notifications\Notifiable;  //Permite enviar notificaciones al usuario
use Laravel\Sanctum\HasApiTokens; //Generar tokens-Usar Sanctum-Autenticación por API

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    //Define qué campos sí pueden llenarse en masa
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    //Define qué campos no deben ser mostrados-no devulver en respuestas JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array 
    {
        return [
            'email_verified_at' => 'datetime', //Convierte a objeto DateTime
            'password' => 'hashed',
        ];
    }

    public function isAdmin()
    {
    return $this->role === 'admin'; // Verifica si el rol del usuario es 'admin'
    }

}
