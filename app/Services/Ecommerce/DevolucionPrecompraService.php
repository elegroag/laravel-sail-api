<?php

namespace App\Services\Ecommerce;

use App\Models\DevolucionPrecompra;
use App\Models\PrecompraServicio;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DevolucionPrecompraService
{
    /**
     * Afiliado solicita devolución formal sobre una precompra VE.
     */
    public function solicitar(PrecompraServicio $precompra, ?string $motivo = null, ?string $notaAfiliado = null): DevolucionPrecompra
    {
        if (! $precompra->isVencida()) {
            throw new RuntimeException('Solo se puede solicitar devolución sobre una compra vencida.');
        }

        if ($precompra->devolucion) {
            throw new RuntimeException('Ya existe una solicitud de devolución para esta compra.');
        }

        return DevolucionPrecompra::create([
            'precompra_id' => $precompra->id,
            'estado' => EstadoDevolucionPrecompra::SOLICITADA,
            'motivo_solicitud' => $motivo !== null && trim($motivo) !== ''
                ? mb_substr(trim($motivo), 0, 255)
                : 'Solicitud formal de devolución por compra vencida',
            'nota_afiliado' => $notaAfiliado !== null && trim($notaAfiliado) !== ''
                ? trim($notaAfiliado)
                : null,
            'fecha_solicitud' => now(),
        ]);
    }

    public function ponerEnTramite(DevolucionPrecompra $devolucion, string $usuarioGestion, ?string $notaGestion = null): DevolucionPrecompra
    {
        if (! $devolucion->isSolicitada()) {
            throw new RuntimeException('Solo una solicitud en estado Solicitada puede pasar a En trámite.');
        }

        $devolucion->fill([
            'estado' => EstadoDevolucionPrecompra::EN_TRAMITE,
            'usuario_gestion' => $usuarioGestion,
            'nota_gestion' => $this->mergeNota($devolucion->nota_gestion, $notaGestion),
            'fecha_en_tramite' => now(),
        ]);
        $devolucion->save();

        return $devolucion->fresh() ?? $devolucion;
    }

    /**
     * Aprueba la devolución: DevolucionPrecompra → APROBADO y precompra → DV.
     */
    public function aprobar(DevolucionPrecompra $devolucion, string $usuarioGestion, ?string $notaGestion = null): DevolucionPrecompra
    {
        if (! $devolucion->estaAbierta()) {
            throw new RuntimeException('La solicitud ya fue resuelta.');
        }

        return DB::transaction(function () use ($devolucion, $usuarioGestion, $notaGestion) {
            $locked = DevolucionPrecompra::whereKey($devolucion->id)->lockForUpdate()->firstOrFail();
            if (! $locked->estaAbierta()) {
                throw new RuntimeException('La solicitud ya fue resuelta.');
            }

            $precompra = PrecompraServicio::whereKey($locked->precompra_id)->lockForUpdate()->firstOrFail();
            if (! $precompra->isVencida() && ! $precompra->isPagado()) {
                throw new RuntimeException('La precompra no admite aprobación de devolución en su estado actual.');
            }

            $locked->fill([
                'estado' => EstadoDevolucionPrecompra::APROBADO,
                'usuario_gestion' => $usuarioGestion,
                'nota_gestion' => $this->mergeNota($locked->nota_gestion, $notaGestion),
                'fecha_en_tramite' => $locked->fecha_en_tramite ?? now(),
                'fecha_resolucion' => now(),
            ]);
            $locked->save();

            $precompra->fill([
                'estado' => EstadoPrecompra::DEVOLUCION,
            ]);
            $precompra->save();

            return $locked->fresh() ?? $locked;
        });
    }

    public function rechazar(DevolucionPrecompra $devolucion, string $usuarioGestion, ?string $notaGestion = null): DevolucionPrecompra
    {
        if (! $devolucion->estaAbierta()) {
            throw new RuntimeException('La solicitud ya fue resuelta.');
        }

        $devolucion->fill([
            'estado' => EstadoDevolucionPrecompra::RECHAZADA,
            'usuario_gestion' => $usuarioGestion,
            'nota_gestion' => $this->mergeNota($devolucion->nota_gestion, $notaGestion),
            'fecha_en_tramite' => $devolucion->fecha_en_tramite ?? now(),
            'fecha_resolucion' => now(),
        ]);
        $devolucion->save();

        return $devolucion->fresh() ?? $devolucion;
    }

    protected function mergeNota(?string $actual, ?string $nueva): ?string
    {
        $nueva = $nueva !== null ? trim($nueva) : '';
        if ($nueva === '') {
            return $actual;
        }

        $actual = $actual !== null ? trim($actual) : '';
        if ($actual === '') {
            return $nueva;
        }

        return $actual . "\n" . $nueva;
    }
}
