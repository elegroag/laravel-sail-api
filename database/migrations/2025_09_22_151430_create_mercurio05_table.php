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
        Schema::create('mercurio05', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas
            $table->char('codofi', 2); // NOT NULL
            $table->char('codciu', 5); // NOT NULL

            // Clave primaria compuesta
            $table->primary(['codciu', 'codofi']);

            // Índice y FK
            $table->index('codofi', 'fk_mercurio05_mercurio041_idx');
            $table->foreign('codofi', 'mercurio05_ibfk_1')
                ->references('codofi')
                ->on('mercurio04');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio05', function (Blueprint $table) {
            $table->dropForeign('mercurio05_ibfk_1');
            $table->dropIndex('fk_mercurio05_mercurio041_idx');
        });
        Schema::dropIfExists('mercurio05');
    }
};
