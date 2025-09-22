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
        Schema::create('mercurio63', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('numero');

            // Columnas según SQL
            $table->char('tipo', 2);
            $table->char('documento', 7);
            $table->char('coddoc', 2);
            $table->string('detalle', 45);
            $table->enum('tipmov', ['S', 'R']);
            $table->integer('movimiento')->nullable();
            $table->integer('valor');
            $table->char('hora', 8);
            $table->date('fecsis');
            $table->char('estado', 1);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio63');
    }
};
