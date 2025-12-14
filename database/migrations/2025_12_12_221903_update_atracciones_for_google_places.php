<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('atracciones', function (Blueprint $table) {

            // 🔴 ELIMINAMOS CAMPOS DE OSM (solo si existen)
            if (Schema::hasColumn('atracciones', 'osm_place_id')) {
                $table->dropColumn('osm_place_id');
            }

            if (Schema::hasColumn('atracciones', 'fuente')) {
                $table->dropColumn('fuente');
            }

            if (Schema::hasColumn('atracciones', 'osm_source')) {
                $table->dropColumn('osm_source');
            }

            // ✅ AGREGAMOS GOOGLE PLACES
            if (!Schema::hasColumn('atracciones', 'google_place_id')) {
                $table->string('google_place_id')->unique()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('atracciones', function (Blueprint $table) {
            if (Schema::hasColumn('atracciones', 'google_place_id')) {
                $table->dropColumn('google_place_id');
            }
        });
    }
};
