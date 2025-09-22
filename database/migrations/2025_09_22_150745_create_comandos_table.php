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
        Schema::create('comandos', function (Blueprint $table) {
            // Motor de almacenamiento como en el SQL original
            $table->engine = 'InnoDB';

            // PK autoincremental int(11)
            $table->increments('id');

            // Campos según definición SQL
            $table->date('fecha_runner')->nullable();
            $table->time('hora_runner')->nullable();
            $table->char('usuario', 6); // NOT NULL
            $table->unsignedTinyInteger('progreso')->default(0); // int(3) NOT NULL DEFAULT '0'
            $table->enum('estado', ['P', 'F', 'X', 'E'])->default('P'); // enum NOT NULL DEFAULT 'P'
            $table->char('proceso', 6)->nullable();
            $table->text('linea_comando'); // NOT NULL
            $table->unsignedInteger('estructura'); // int(11) NOT NULL (unsigned para FK)
            $table->string('parametros', 250); // NOT NULL
            $table->text('resultado')->nullable();

            // Índice y clave foránea, con nombres como en el SQL
            $table->index('estructura', 'comandos_FK');
            $table->foreign('estructura', 'comandos_FK')
                ->references('id')
                ->on('comando_estructuras')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('comandos', function (Blueprint $table) {
            // Eliminar FK e índice antes de dropear la tabla
            $table->dropForeign('comandos_FK');
            $table->dropIndex('comandos_FK');
        });

        Schema::dropIfExists('comandos');
    }
};
