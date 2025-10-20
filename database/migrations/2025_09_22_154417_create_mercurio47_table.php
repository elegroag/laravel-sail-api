<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mercurio47', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();

            $table->integer('documento');
            $table->char('tipo', 2);
            $table->char('coddoc', 2);
            $table->char('estado', 1);
            $table->date('fecest');
            $table->date('fecsol');
            $table->char('tipact', 1);
            $table->integer('usuario')->nullable();
            $table->index('documento', 'documento');
            $table->index('tipo', 'tipo');
            $table->index('coddoc', 'coddoc');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mercurio47');
    }
};
