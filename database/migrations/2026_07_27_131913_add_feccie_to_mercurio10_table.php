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
            $table->date('feccie')->nullable()->after('cerrada');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio10', function (Blueprint $table) {
            $table->dropColumn('feccie');
        });
    }
};
