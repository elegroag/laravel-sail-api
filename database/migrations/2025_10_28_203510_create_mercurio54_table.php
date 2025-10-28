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
        Schema::create('mercurio54', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';
            // Columnas
            $table->string('tipo', 2);
            $table->string('coddoc', 1);
            $table->string('documento', 15);
            $table->string('token', 255);
            $table->string('tokencel', 255)->nullable();
            $table->string('tiptra', 2)->nullable();
            $table->string('codtra', 2)->nullable();
            $table->string('doctra', 15)->nullable();

            $table->primary(['tipo', 'coddoc', 'documento']);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio54');
    }
};
