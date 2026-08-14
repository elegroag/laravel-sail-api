<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Historial de respuestas ePayco para auditoría de pagos.
     * Una precompra puede tener varias filas (revalidaciones / reintentos).
     */
    public function up(): void
    {
        Schema::create('epayco_transacciones', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->unsignedInteger('precompra_id')->nullable();

            $table->string('ref_payco', 80)->nullable();
            $table->string('transaction_id', 50)->nullable();
            $table->string('invoice', 60)->nullable();
            $table->string('approval_code', 20)->nullable();

            $table->string('cod_estado', 2)->nullable()->comment('x_cod_transaction_state');
            $table->string('respuesta', 100)->nullable()->comment('x_response');
            $table->string('motivo', 255)->nullable()->comment('x_response_reason_text');

            $table->decimal('amount', 12, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->string('bank_name', 80)->nullable();
            $table->string('franchise', 40)->nullable();
            $table->string('card_mask', 30)->nullable();
            $table->string('quotas', 10)->nullable();
            $table->string('signature', 255)->nullable();
            $table->string('fecha_epayco', 40)->nullable()->comment('x_date tal como lo envía ePayco');

            $table->string('origen', 30)->default('validacion')->comment('validacion|webhook|manual');
            $table->json('payload_json')->nullable()->comment('respuesta cruda data de ePayco');

            $table->timestamps();

            $table->index('ref_payco');
            $table->index('transaction_id');
            $table->index('precompra_id');
            $table->index(['precompra_id', 'created_at']);

            $table->foreign('precompra_id')
                ->references('id')
                ->on('precompras_servicios')
                ->nullOnDelete();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('epayco_transacciones');
    }
};
