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
        Schema::create('mercurio07', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas
            $table->char('tipo', 2); // NOT NULL
            $table->char('coddoc', 2); // NOT NULL
            $table->char('documento', 15); // NOT NULL
            $table->string('nombre', 120); // NOT NULL
            $table->char('email', 60); // NOT NULL
            $table->char('clave', 255); // NOT NULL
            $table->date('feccla'); // NOT NULL
            $table->char('autoriza', 1); // NOT NULL
            $table->char('codciu', 5); // NOT NULL
            $table->date('fecreg'); // NOT NULL
            $table->enum('estado', ['A', 'I', 'P', 'X']); // NOT NULL
            $table->date('fecha_syncron')->nullable();
            $table->char('whatsapp', 10)->nullable();

            // PK compuesta
            $table->primary(['tipo', 'coddoc', 'documento']);

            // Índice y FK
            $table->index('tipo', 'fk_mercurio07_mercurio061_idx');
            $table->foreign('tipo', 'fk_mercurio07_mercurio061')
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
        Schema::table('mercurio07', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio07_mercurio061');
            $table->dropIndex('fk_mercurio07_mercurio061_idx');
        });
        Schema::dropIfExists('mercurio07');
    }
};
