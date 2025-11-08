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
        Schema::create('radicados', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->primary('id');
            $table->char('vigencia', 4);
            $table->char('tipo', 3);
            $table->integer('numero');
            $table->char('radicado', 15);
            $table->unique(['vigencia', 'tipo', 'numero'], 'radicado_unique');
            $table->index(['vigencia', 'tipo', 'numero'], 'radicado_index');
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('radicados');
    }
};
