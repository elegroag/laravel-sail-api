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
        Schema::create('mercurio52', function (Blueprint $table) {
            // Motor MyISAM (según SQL original)
            $table->engine = 'MyISAM';

            // Clave primaria entera NO autoincremental
            $table->integer('codmen');
            $table->primary('codmen');

            // Columnas
            $table->string('detalle', 45);
            $table->integer('codare');
            $table->string('url', 45);
            $table->char('tipo', 2);
            $table->enum('estado', ['A', 'I']);

            // Índice (sin FK ya que MyISAM no soporta claves foráneas)
            $table->index('codare', 'fk_mercurio52_mercurio551_idx');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio52');
    }
};
