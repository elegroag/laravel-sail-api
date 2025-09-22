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
        Schema::create('mercurio26', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas
            $table->integer('numero'); // NOT NULL
            $table->char('archivo', 45); // NOT NULL
            $table->integer('orden'); // NOT NULL
            $table->enum('tipo', ['F', 'V'])->nullable()->comment('V = Foto, V = Video');

            // PK
            $table->primary('numero');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio26');
    }
};
