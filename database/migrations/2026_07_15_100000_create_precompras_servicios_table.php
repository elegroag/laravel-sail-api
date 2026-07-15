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
        Schema::create('precompras_servicios', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');

            // Datos de la compra
            $table->string('documento', 20)->comment('cedtra del usuario en sesion');
            $table->string('codser', 20);
            $table->integer('numero');
            $table->string('codben', 20)->nullable()->comment('beneficiario (codben o cedtra)');
            $table->text('nota')->nullable();
            $table->decimal('valor', 12, 2)->nullable()->comment('monto del servicio al momento de la precompra');

            // Estado: PE=pendiente, PA=pagado, DE=desestimado, RE=rechazado
            $table->string('estado', 2)->default('PE');

            // Datos de la transaccion ePayco
            $table->string('ref_payco', 80)->nullable();
            $table->string('cod_estado_epayco', 2)->nullable();
            $table->string('motivo_epayco', 255)->nullable();

            $table->timestamp('fecha_precompra')->useCurrent();
            $table->timestamp('fecha_pago')->nullable();
            $table->timestamps();

            $table->index(['documento', 'estado']);
            $table->index('ref_payco');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('precompras_servicios');
    }
};
