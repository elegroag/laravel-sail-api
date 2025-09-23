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
        Schema::create('mercurio09', function (Blueprint $table) {
            // Motor InnoDB como en el SQL
            $table->engine = 'InnoDB';

            // Clave primaria CHAR(2)
            $table->char('tipopc', 2);
            $table->primary('tipopc');

            // Columnas
            $table->string('detalle', 45);
            $table->integer('dias')->nullable(); // int(11) NULL
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio09');
    }
};
