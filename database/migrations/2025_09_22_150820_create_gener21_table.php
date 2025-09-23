<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::create('gener21', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Clave primaria en CHAR(4)
            $table->char('tipfun', 4);
            $table->primary('tipfun');

            // Columnas
            $table->char('detalle', 140);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('gener21');
    }
};
