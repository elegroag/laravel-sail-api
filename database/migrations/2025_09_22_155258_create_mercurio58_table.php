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
        Schema::create('mercurio58', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->integer('numero')->autoIncrement();
            $table->primary('numero');

            // Columnas
            $table->string('archivo', 100);
            $table->integer('orden');
            $table->integer('codare');

            // Índice y FK
            $table->index('codare', 'fk_mercurio58_mercurio551');
            $table->foreign('codare', 'fk_mercurio58_mercurio551')
                ->references('codare')
                ->on('mercurio55')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio58', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio58_mercurio551');
            $table->dropIndex('fk_mercurio58_mercurio551');
        });
        Schema::dropIfExists('mercurio58');
    }
};
