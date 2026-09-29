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
        Schema::create('carnet_tokens', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('token', 32)->unique();
            $table->string('coddoc', 4);
            $table->string('documento', 20);
            $table->char('estado', 1)->default('A');
            $table->dateTime('fecha_creacion');
            $table->dateTime('ultima_verificacion')->nullable();
            $table->unsignedInteger('verificaciones')->default(0);
            $table->unique(['coddoc', 'documento'], 'carnet_tokens_afiliado');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('carnet_tokens');
    }
};
