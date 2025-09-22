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
        Schema::create('gener42', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas
            $table->integer('usuario')->default(0); // int(11) NOT NULL DEFAULT '0'
            $table->char('permiso', 10); // char(10) NOT NULL

            // Clave primaria compuesta
            $table->primary(['usuario', 'permiso']);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('gener42');
    }
};
