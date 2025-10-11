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
        Schema::create('mercurio15', function (Blueprint $table) {
            // Motor y charset como en el SQL
            $table->engine = 'InnoDB';

            // id int(2) NOT NULL AUTO_INCREMENT (firmado)
            $table->integer('id')->autoIncrement();

            // Columnas
            $table->char('estado', 8)->nullable();
            $table->string('detalle', 120)->nullable();
            $table->char('tipo_rural', 1)->default('N');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio15');
    }
};
