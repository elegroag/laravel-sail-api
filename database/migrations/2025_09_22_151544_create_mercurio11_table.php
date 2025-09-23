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
        Schema::create('mercurio11', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Clave primaria CHAR(2)
            $table->char('codest', 2);
            $table->primary('codest');

            // Columnas
            $table->string('detalle', 45);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio11');
    }
};
