<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Retira el módulo deprecado de firmas autorizadas (cajas/mercurio03).
     */
    public function up(): void
    {
        Schema::dropIfExists('mercurio03');
    }

    /**
     * Recrea la estructura básica de mercurio03 si se revierte.
     */
    public function down(): void
    {
        if (Schema::hasTable('mercurio03')) {
            return;
        }

        Schema::create('mercurio03', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->char('codfir', 3);
            $table->primary('codfir');
            $table->char('nombre', 60);
            $table->char('cargo', 60);
            $table->char('archivo', 30)->nullable();
            $table->char('email', 60)->nullable();
        });
    }
};
