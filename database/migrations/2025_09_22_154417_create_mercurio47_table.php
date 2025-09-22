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
        Schema::create('mercurio47', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // id int(15) NOT NULL AUTO_INCREMENT (firmado)
            $table->integer('id')->autoIncrement();
            $table->primary('id');

            // Columnas
            $table->integer('documento');
            $table->char('tipo', 2);
            $table->char('coddoc', 2);
            $table->char('estado', 1);
            $table->date('fecha_estado');
            $table->date('fecha_solicitud');
            $table->char('tipo_actualizacion', 1);
            $table->integer('usuario')->nullable();

            // Índices
            $table->index('documento', 'documento');
            $table->index('tipo', 'tipo');
            $table->index('coddoc', 'coddoc');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio47');
    }
};
