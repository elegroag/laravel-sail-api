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
        Schema::create('gener09', function (Blueprint $table) {
            // Motor como el SQL original
            $table->engine = 'InnoDB';

            // id int(5) NOT NULL AUTO_INCREMENT (firmado)
            $table->integer('id')->autoIncrement(); // autoincremental, ya es clave primaria

            // Campos
            $table->char('codzon', 9); // NOT NULL
            $table->char('detzon', 30); // NOT NULL

            // Índices
            $table->unique('codzon', 'codzon');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('gener09');
    }
};
