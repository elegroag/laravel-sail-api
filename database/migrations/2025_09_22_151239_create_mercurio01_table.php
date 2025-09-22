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
        Schema::create('mercurio01', function (Blueprint $table) {
            // Motor InnoDB como en el SQL
            $table->engine = 'InnoDB';

            // Clave primaria CHAR(2)
            $table->char('codapl', 2);
            $table->primary('codapl');

            // Columnas
            $table->char('email', 45)->nullable();
            $table->char('clave', 20)->nullable();
            $table->string('path', 45); // NOT NULL
            $table->string('ftpserver', 45)->nullable();
            $table->string('pathserver', 45)->nullable();
            $table->string('userserver', 45)->nullable();
            $table->string('passserver', 45)->nullable();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio01');
    }
};
