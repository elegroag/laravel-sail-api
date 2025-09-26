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
        Schema::create('mercurio59', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas
            $table->char('codinf', 30);
            $table->char('codser', 7);
            $table->integer('numero');
            $table->string('archivo', 100)->nullable();
            $table->string('nota', 255);
            $table->string('email', 60);
            $table->enum('precan', ['S', 'N']);
            $table->enum('autser', ['S', 'N']);
            $table->enum('consumo', ['S', 'N'])->nullable();
            $table->enum('estado', ['A', 'I']);
            $table->date('fecini')->nullable();
            $table->date('fecfin')->nullable();

            // PK compuesta
            $table->primary(['codinf', 'codser', 'numero']);

            // Índice y FK
            $table->index('codinf', 'fk_mercurio59_mercurio561_idx');
            $table->foreign('codinf', 'fk_mercurio59_mercurio561')
                ->references('codinf')
                ->on('mercurio56')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio59', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio59_mercurio561');
            $table->dropIndex('fk_mercurio59_mercurio561_idx');
        });
        Schema::dropIfExists('mercurio59');
    }
};
