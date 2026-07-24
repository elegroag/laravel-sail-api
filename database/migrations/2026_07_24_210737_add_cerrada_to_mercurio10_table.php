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
        Schema::table('mercurio10', function (Blueprint $table) {
            $table->char('cerrada', 1)->default('N')->after('ruuid');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio10', function (Blueprint $table) {
            $table->dropColumn('cerrada');
        });
    }
};
