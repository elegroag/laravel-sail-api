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
        Schema::create('recepcionsat', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental BIGINT UNSIGNED
            $table->bigIncrements('id');

            // Columnas según SQL
            $table->text('contenido');
            $table->string('numero_transaccion', 200);
            $table->timestamp('fecha')->useCurrent();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepcionsat');
    }
};
