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
        Schema::create('pines_afiliado', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('id');

            // Columnas según SQL
            $table->integer('pin');
            $table->date('fecha')->nullable();
            $table->bigInteger('cedtra');
            $table->bigInteger('docben');
            $table->enum('estado', ['A', 'I', 'R'])->nullable();
            $table->string('codser', 20);
            $table->date('fecres')->nullable();
            $table->time('horres')->nullable();
            $table->date('fecent')->nullable();
            $table->time('horent')->nullable();
            $table->enum('medio', ['A', 'E', 'P'])->nullable();
            $table->char('userent', 4)->nullable();

            // Índices
            $table->unique('pin', 'pin');
            $table->index('cedtra', 'cedtra_PA_FK');
            $table->index('docben', 'docben_PA_FK');
            $table->index('codser', 'codser_PA_FK');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('pines_afiliado');
    }
};
