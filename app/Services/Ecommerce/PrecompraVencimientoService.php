<?php

namespace App\Services\Ecommerce;

use App\Models\PrecompraServicio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrecompraVencimientoService
{
    public function __construct(
        protected VencimientoSubsidioDetector $detector = new VencimientoSubsidioDetector
    ) {}

    /**
     * Si el mensaje de Subsidio indica cupos/plazo y la precompra está PA,
     * la marca como VE. No modifica otros estados.
     *
     * @return array{marcada: bool, precompra: PrecompraServicio, motivo: ?string}
     */
    public function marcarSiAplica(PrecompraServicio $precompra, ?string $mensajeSubsidio): array
    {
        $clasificacion = $this->detector->clasificar($mensajeSubsidio);
        if (! ($clasificacion['vencida'] ?? false)) {
            return ['marcada' => false, 'precompra' => $precompra, 'motivo' => null];
        }

        $precompra = $precompra->fresh() ?? $precompra;
        if (! $precompra->isPagado()) {
            return ['marcada' => false, 'precompra' => $precompra, 'motivo' => $clasificacion['motivo']];
        }

        $motivo = (string) ($clasificacion['motivo'] ?? EstadoPrecompra::MOTIVO_VENCIDA_PLAZO);
        $detalle = mb_substr(trim((string) $mensajeSubsidio), 0, 1000);

        DB::transaction(function () use ($precompra, $motivo, $detalle) {
            $locked = PrecompraServicio::whereKey($precompra->id)->lockForUpdate()->first();
            if (! $locked || ! $locked->isPagado()) {
                return;
            }

            $locked->fill([
                'estado' => EstadoPrecompra::VENCIDA,
                'motivo_vencida' => $motivo,
                'detalle_vencida' => $detalle !== '' ? $detalle : null,
                'fecha_vencida' => now(),
            ]);
            $locked->save();
        });

        $fresh = $precompra->fresh() ?? $precompra;

        Log::info('Precompra marcada VENCIDA por rechazo Subsidio', [
            'precompra_id' => $fresh->id,
            'motivo' => $fresh->motivo_vencida,
        ]);

        return [
            'marcada' => $fresh->isVencida(),
            'precompra' => $fresh,
            'motivo' => $fresh->motivo_vencida,
        ];
    }

    /**
     * Marca manualmente una precompra PA como VE (operador Cajas).
     */
    public function marcarManual(PrecompraServicio $precompra, string $motivo, ?string $detalle = null): PrecompraServicio
    {
        if (! array_key_exists($motivo, EstadoPrecompra::MOTIVOS_VENCIDA)) {
            throw new \InvalidArgumentException('Motivo de vencimiento inválido.');
        }

        if (! $precompra->isPagado()) {
            throw new \RuntimeException('Solo se puede marcar como vencida una precompra pagada.');
        }

        $precompra->fill([
            'estado' => EstadoPrecompra::VENCIDA,
            'motivo_vencida' => $motivo,
            'detalle_vencida' => $detalle !== null && trim($detalle) !== '' ? mb_substr(trim($detalle), 0, 1000) : null,
            'fecha_vencida' => now(),
        ]);
        $precompra->save();

        return $precompra->fresh() ?? $precompra;
    }
}
