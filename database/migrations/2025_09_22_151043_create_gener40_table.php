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
        Schema::create('gener40', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Clave primaria CHAR(5)
            $table->char('codigo', 5);
            $table->primary('codigo');

            // Columnas
            $table->char('detalle', 255)->nullable();
            $table->integer('orden')->nullable(); // int(5) NULL
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('gener40');
    }
};
