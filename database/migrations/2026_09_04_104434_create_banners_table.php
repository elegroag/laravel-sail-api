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
        Schema::create('banners', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->text('content_html')->nullable();
            $table->string('imagen', 255)->nullable();
            $table->string('url_imagen', 500)->nullable();
            $table->date('fecha_inicia');
            $table->date('fecha_finaliza');
            $table->string('estado', 1);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
