<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('precompras_servicios', function (Blueprint $table) {
            $table->string('motivo_vencida', 50)->nullable()->after('fecha_desestimacion')
                ->comment('VENCIDA_SIN_CUPOS | VENCIDA_PLAZO');
            $table->text('detalle_vencida')->nullable()->after('motivo_vencida');
            $table->timestamp('fecha_vencida')->nullable()->after('detalle_vencida');
        });

        Schema::create('devolucion_precompra', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->unsignedInteger('precompra_id');
            $table->string('estado', 20)->default('SOLICITADA');
            $table->string('motivo_solicitud', 255)->nullable();
            $table->text('nota_afiliado')->nullable();
            $table->text('nota_gestion')->nullable();
            $table->string('usuario_gestion', 50)->nullable();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_en_tramite')->nullable();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->timestamps();

            $table->unique('precompra_id');
            $table->index('estado');
            $table->foreign('precompra_id')
                ->references('id')
                ->on('precompras_servicios')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devolucion_precompra');

        Schema::table('precompras_servicios', function (Blueprint $table) {
            $table->dropColumn(['motivo_vencida', 'detalle_vencida', 'fecha_vencida']);
        });
    }
};
