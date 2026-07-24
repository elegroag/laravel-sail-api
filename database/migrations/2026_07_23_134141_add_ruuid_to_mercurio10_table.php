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
            $table->string('ruuid', 20)->nullable()->after('campos_corregir');
            $table->index('ruuid', 'fk_mercurio10_ruuid_idx');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio10', function (Blueprint $table) {
            $table->dropIndex('fk_mercurio10_ruuid_idx');
            $table->dropColumn('ruuid');
        });
    }
};
