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
        Schema::create('atracciones', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');              // Nombre de la atracción
        $table->text('descripcion');           // Descripción detallada
        $table->string('categoria');           // Ej: naturaleza, cultura, aventura
        $table->string('ubicacion');           // Dirección o referencia
        $table->decimal('precio', 8, 2)->nullable(); // Precio opcional
        $table->string('imagen_url')->nullable();    // URL de imagen (Firebase)
        $table->timestamps();
    });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atraccions');
    }
};
