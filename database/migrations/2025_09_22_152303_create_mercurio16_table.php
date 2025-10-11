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
        Schema::create('mercurio16', function (Blueprint $table) {
            // Motor InnoDB como en el SQL
            $table->engine = 'InnoDB';

            // PK bigint(15) AUTO_INCREMENT (firmado)
            $table->bigInteger('id')->autoIncrement();

            // Columnas
            $table->char('documento', 15); // NOT NULL
            $table->char('coddoc', 2); // NOT NULL
            $table->string('firma', 180); // NOT NULL
            $table->date('fecha'); // NOT NULL
            $table->text('keyprivate')->nullable();
            $table->text('keypublic')->nullable();
            $table->string('password', 250)->nullable();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio16');
    }
};
