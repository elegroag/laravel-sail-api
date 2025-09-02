<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nucleos_familiares', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('rut')->unique();
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['masculino', 'femenino', 'otro']);
            $table->enum('parentesco', ['conyuge', 'hijo', 'hija', 'padre', 'madre', 'hermano', 'hermana', 'otro']);
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->enum('estado_civil', ['soltero', 'casado', 'divorciado', 'viudo', 'union_libre'])->nullable();
            $table->string('ocupacion')->nullable();
            $table->boolean('dependiente_economico')->default(false);
            $table->foreignId('trabajador_id')->constrained('trabajadores')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nucleos_familiares');
    }
};
