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
        Schema::create('mercurio28', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas
            $table->char('tipo', 2); // NOT NULL
            $table->char('campo', 20); // NOT NULL
            $table->char('detalle', 45); // NOT NULL
            $table->integer('orden'); // NOT NULL

            // PK compuesta
            $table->primary(['tipo', 'campo']);

            // Índice y FK
            $table->index('tipo', 'fk_mercurio28_mercurio061_idx');
            $table->foreign('tipo', 'fk_mercurio28_mercurio061')
                ->references('tipo')
                ->on('mercurio06')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio28', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio28_mercurio061');
            $table->dropIndex('fk_mercurio28_mercurio061_idx');
        });
        Schema::dropIfExists('mercurio28');
    }
};
