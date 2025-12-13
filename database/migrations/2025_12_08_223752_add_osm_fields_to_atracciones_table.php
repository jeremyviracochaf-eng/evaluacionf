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
            
            $table->decimal('lat', 10, 7)->nullable()->after('ubicacion');
            $table->decimal('lon', 10, 7)->nullable()->after('lat');
            $table->string('direccion', 500)->nullable()->after('lon'); // display_name de OSM
            $table->string('osm_place_id')->nullable()->after('direccion');
            $table->string('osm_source')->default('nominatim')->after('osm_place_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atracciones', function (Blueprint $table) {
            
            $table->dropColumn(['lat', 'lon', 'direccion', 'osm_place_id', 'osm_source']);    

        });
    }
};
