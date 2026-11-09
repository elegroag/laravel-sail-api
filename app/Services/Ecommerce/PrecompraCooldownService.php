<?php

namespace App\Services\Ecommerce;

use App\Models\PrecompraServicio;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Bloqueo temporal de nuevas compras del mismo servicio + beneficiario
 * mientras exista una precompra pendiente (PE) reciente.
 */
class PrecompraCooldownService
{
    public const TTL_MINUTES = 20;

    public const CODE = 'PRECOMPRA_COOLDOWN';

    /**
     * Precompras PE vigentes (dentro del TTL) del documento.
     *
     * @return Collection<int, PrecompraServicio>
     */
    public function vigentes(string $documento, ?string $codser = null, ?int $numero = null): Collection
    {
        $query = PrecompraServicio::where('documento', $documento)
            ->where('estado', EstadoPrecompra::PENDIENTE)
            ->where('fecha_precompra', '>=', now()->subMinutes(self::TTL_MINUTES));

        if ($codser !== null) {
            $query->where('codser', $codser);
        }
        if ($numero !== null) {
            $query->where('numero', $numero);
        }

        return $query->orderBy('fecha_precompra')->get();
    }

    /**
     * Retorna el bloqueo activo (el que libera mas tarde) o null.
     *
     * @param  list<string>  $codbens
     */
    public function findBloqueo(string $documento, string $codser, int $numero, array $codbens): ?array
    {
        return $this->resolverBloqueo($this->vigentes($documento, $codser, $numero), $codbens);
    }

    /**
     * Elige el bloqueo que libera mas tarde entre precompras vigentes que
     * comparten algun codben con la solicitud.
     *
     * @param  Collection<int, PrecompraServicio>  $vigentes
     * @param  list<string>  $codbens
     */
    public function resolverBloqueo(Collection $vigentes, array $codbens): ?array
    {
        $codbens = array_values(array_unique(array_map('strval', $codbens)));
        $bloqueo = null;

        foreach ($vigentes as $precompra) {
            $comunes = array_values(array_intersect($precompra->codbenList(), $codbens));
            if ($comunes === []) {
                continue;
            }

            $item = $this->formatear($precompra, $comunes);
            if ($bloqueo === null || $item['segundos_restantes'] > $bloqueo['segundos_restantes']) {
                $bloqueo = $item;
            }
        }

        return $bloqueo;
    }

    /**
     * Lista de bloqueos activos del documento para la UI.
     *
     * @return list<array<string, mixed>>
     */
    public function listarBloqueos(string $documento): array
    {
        return $this->vigentes($documento)
            ->map(fn (PrecompraServicio $p) => $this->formatear($p, $p->codbenList()))
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $codbens
     */
    protected function formatear(PrecompraServicio $precompra, array $codbens): array
    {
        /** @var CarbonInterface $inicio */
        $inicio = $precompra->fecha_precompra;
        $disponibleEn = $inicio->copy()->addMinutes(self::TTL_MINUTES);
        $segundos = max(0, (int) now()->diffInSeconds($disponibleEn, false));

        return [
            'precompra_id' => $precompra->id,
            'codser' => (string) $precompra->codser,
            'numero' => (int) $precompra->numero,
            'codbens' => $codbens,
            'segundos_restantes' => $segundos,
            'disponible_en' => $disponibleEn->toIso8601String(),
        ];
    }
}
