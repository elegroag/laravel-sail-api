<?php

namespace App\Jobs;

use App\Models\PrecompraServicio;
use App\Services\Ecommerce\EstadoPrecompra;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Marca como ABANDONADA (AB) las precompras pendientes (PE)
 * cuya fecha_precompra supera el TTL configurado.
 */
class MarcarPrecomprasAbandonadas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(
        public int $dias = 7,
        public bool $dryRun = false,
    ) {
        if ($this->dias < 1) {
            $this->dias = 1;
        }
    }

    /**
     * @return int Cantidad de candidatas (dry-run) o filas actualizadas
     */
    public function handle(): int
    {
        $limite = Carbon::now()->subDays($this->dias);

        $query = PrecompraServicio::query()
            ->where('estado', EstadoPrecompra::PENDIENTE)
            ->where('fecha_precompra', '<', $limite);

        $candidatas = (clone $query)->count();

        if ($this->dryRun) {
            Log::info('MarcarPrecomprasAbandonadas dry-run', [
                'dias' => $this->dias,
                'limite' => $limite->toDateTimeString(),
                'candidatas' => $candidatas,
            ]);

            return $candidatas;
        }

        if ($candidatas === 0) {
            return 0;
        }

        $updated = $query->update([
            'estado' => EstadoPrecompra::ABANDONADA,
            'motivo_desestimacion' => EstadoPrecompra::MOTIVO_ABANDONO_INACTIVIDAD,
            'detalle_desestimacion' => "Abandonada automaticamente por inactividad (>{$this->dias} dias)",
            'fecha_desestimacion' => now(),
        ]);

        Log::info('MarcarPrecomprasAbandonadas ejecutado', [
            'dias' => $this->dias,
            'limite' => $limite->toDateTimeString(),
            'actualizadas' => $updated,
        ]);

        return $updated;
    }
}
