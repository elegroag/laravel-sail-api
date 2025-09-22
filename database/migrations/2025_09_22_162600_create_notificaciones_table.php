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
        Schema::create('notificaciones', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('id');

            // Columnas según SQL
            $table->string('titulo', 140)->nullable();
            $table->string('descri', 224)->nullable();
            $table->integer('user')->nullable();
            $table->char('estado', 1)->nullable();
            $table->integer('progre')->nullable();
            $table->string('result', 224)->nullable();
            $table->date('dia')->nullable();
            $table->time('hora')->nullable();

            // Índice
            $table->index('user', 'notificaciones_user_index');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
