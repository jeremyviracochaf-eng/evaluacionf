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
    Schema::table('atracciones', function (Blueprint $table) {
        $table->dropColumn([
            'osm_place_id',
            'direccion',
            'lat',
            'lon',
        ]);
    });
}


public function down(): void
{
    Schema::table('atracciones', function (Blueprint $table) {
        $table->decimal('lat', 10, 7)->nullable();
        $table->decimal('lon', 10, 7)->nullable();
        $table->string('direccion')->nullable();
        $table->bigInteger('osm_place_id')->nullable();
    });
}

};
