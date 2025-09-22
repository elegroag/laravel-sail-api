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
        Schema::create('afiliado_habil', function (Blueprint $table) {
            // Motor de almacenamiento (coincide con InnoDB del SQL original)
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->id();

            // Campos según definición SQL
            $table->bigInteger('cedtra')->nullable(); // bigint(16) NULL
            $table->bigInteger('docben')->nullable(); // bigint(16) NULL
            $table->char('categoria', 2)->nullable(); // char(2) NULL
            $table->string('nomben', 120)->nullable(); // varchar(120) NULL
            $table->string('codser', 20); // varchar(20) NOT NULL
            $table->integer('pin'); // int(10) NOT NULL

            // Índices según SQL
            $table->unique('pin', 'afiliado_habil_pin_unique'); // UNIQUE KEY `pin` (`pin`)
            $table->index('codser', 'codser_AH_FK'); // KEY `codser_AH_FK` (`codser`)
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('afiliado_habil');
    }
};
