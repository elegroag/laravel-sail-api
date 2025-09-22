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
        Schema::create('gener02', function (Blueprint $table) {
            // Motor como el SQL original
            $table->engine = 'InnoDB';

            // PK no autoincremental
            $table->integer('usuario'); // int(11) NOT NULL
            $table->primary('usuario');

            // Columnas
            $table->string('nombre', 45); // NOT NULL
            $table->char('tipfun', 4); // NOT NULL
            $table->string('email', 60)->nullable();
            $table->string('login', 20)->nullable();
            $table->string('criptada', 200)->nullable();
            $table->enum('acceso', ['R','H'])->default('H');
            $table->enum('estado', ['A','I','B'])->default('A');
            $table->bigInteger('cedtra')->nullable(); // bigint(15) NULL
            $table->string('clave', 80)->nullable();

            // Índice y FK según SQL
            $table->index('tipfun', 'tipfun');
            $table->foreign('tipfun', 'gener02_ibfk_1')
                ->references('tipfun')
                ->on('gener21');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('gener02', function (Blueprint $table) {
            $table->dropForeign('gener02_ibfk_1');
            $table->dropIndex('tipfun');
        });
        Schema::dropIfExists('gener02');
    }
};
