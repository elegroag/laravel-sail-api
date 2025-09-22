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
        Schema::create('mercurio65', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('codsed');

            // Columnas según SQL
            $table->char('nit', 15);
            $table->string('razsoc', 100);
            $table->string('direccion', 60);
            $table->string('email', 60);
            $table->string('celular', 10);
            $table->integer('codcla');
            $table->string('detalle', 45);
            $table->string('archivo', 100);
            $table->enum('estado', ['A', 'I']);
            $table->char('lat', 20)->nullable();
            $table->char('log', 20)->nullable();

            // Índice para FK
            $table->index('codcla', 'fk_mercurio65_mercurio671_idx');

            // FK hacia mercurio67(codcla) con RESTRICT (NO ACTION)
            $table->foreign('codcla', 'fk_mercurio65_mercurio671')
                ->references('codcla')
                ->on('mercurio67')
                ->restrictOnDelete()
                ->restrictOnUpdate();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio65');
    }
};
