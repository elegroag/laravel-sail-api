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
        Schema::create('comando_estructuras', function (Blueprint $table) {
            // Motor de almacenamiento como en el SQL original
            $table->engine = 'InnoDB';

            // PK autoincremental int(11)
            $table->increments('id');

            // Campos según la definición SQL
            $table->char('procesador', 10)->nullable();
            $table->string('estructura', 255)->nullable();
            $table->string('variables', 200)->nullable();
            $table->string('tipo', 45)->nullable();
            $table->string('sistema', 45)->nullable();
            $table->char('env', 1)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->tinyInteger('asyncro')->default(1); // NOT NULL DEFAULT '1'
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('comando_estructuras');
    }
};
