<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('atracciones', function (Blueprint $table) {
            // Hacer google_place_id nullable
            $table->string('google_place_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('atracciones', function (Blueprint $table) {
            $table->string('google_place_id')->nullable(false)->change();
        });
    }
};
