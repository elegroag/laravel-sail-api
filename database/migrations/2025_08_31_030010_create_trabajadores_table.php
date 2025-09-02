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
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('rut')->unique();
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['masculino', 'femenino', 'otro']);
            $table->string('direccion');
            $table->string('cargo');
            $table->decimal('salario', 10, 2);
            $table->date('fecha_ingreso');
            $table->date('fecha_salida')->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'suspendido'])->default('activo');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
