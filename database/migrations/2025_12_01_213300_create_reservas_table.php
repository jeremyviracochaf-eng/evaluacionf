<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
        $table->id();
        
        // Relación con usuarios
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        // Relación con atracciones
        $table->foreignId('atraccion_id')->constrained('atracciones')->onDelete('cascade');
        
        // Datos de la reserva
        $table->date('fecha');                  // Fecha de la visita
        $table->time('hora');                   // Hora de la visita
        $table->enum('estado', ['pendiente','aceptada','rechazada'])->default('pendiente');
        $table->text('comentarios')->nullable(); // Opcional
        
        $table->timestamps();
    });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
