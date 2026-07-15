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
        Schema::table('precompras_servicios', function (Blueprint $table) {
            $table->string('motivo_desestimacion', 50)->nullable()->after('motivo_epayco')->comment('codigo del motivo de desestimacion');
            $table->string('detalle_desestimacion', 255)->nullable()->after('motivo_desestimacion')->comment('texto libre cuando el motivo es OTRO');
            $table->timestamp('fecha_desestimacion')->nullable()->after('fecha_pago');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('precompras_servicios', function (Blueprint $table) {
            $table->dropColumn(['motivo_desestimacion', 'detalle_desestimacion', 'fecha_desestimacion']);
        });
    }
};
