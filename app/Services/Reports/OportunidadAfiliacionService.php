<?php

namespace App\Services\Reports;

use App\Models\Mercurio31;
use App\Support\AfiliacionNormalizer;
use App\Support\DiasHabilesCalculator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OportunidadAfiliacionService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildDataset(array $filtros = []): array
    {
        $tipos = $this->resolveTipos($filtros['tipafis'] ?? null);
        $fecini = $filtros['fecini'] ?? null;
        $fecfin = $filtros['fecfin'] ?? null;
        $nit = isset($filtros['nit']) ? trim((string) $filtros['nit']) : '';
        $cedtra = isset($filtros['cedtra']) ? trim((string) $filtros['cedtra']) : '';
        $cedcon = isset($filtros['cedcon']) ? trim((string) $filtros['cedcon']) : '';
        $numdoc = isset($filtros['numdoc']) ? trim((string) $filtros['numdoc']) : '';
        $umbral = (int) config('reportes.oportunidad_umbral_dias', 3);

        $titularesIndex = $this->buildTitularesIndex();
        $dataset = [];

        foreach ($tipos as $tipopc => $config) {
            $query = $config['model']::query();

            if ($fecini && $fecfin) {
                $query->whereBetween('fecsol', [
                    $fecini.' 00:00:00',
                    Carbon::parse($fecfin)->endOfDay()->format('Y-m-d H:i:s'),
                ]);
            }

            $this->aplicarFiltrosDocumento($query, $config, $nit, $cedtra, $cedcon, $numdoc);

            $query->orderBy('fecsol')->orderBy('id');

            foreach ($query->cursor() as $model) {
                $record = AfiliacionNormalizer::normalize($model, (int) $tipopc, $config, $titularesIndex);

                $fin = $record['fecha_cierre'];
                $record['dias_habiles'] = DiasHabilesCalculator::between($record['fecsol'], $fin);
                $record['estado_oportunidad'] = $this->resolverEstadoOportunidad(
                    $record['fecapr'],
                    $record['dias_habiles'],
                    $umbral
                );

                $dataset[] = $record;
            }
        }

        usort($dataset, function (array $a, array $b): int {
            $fechaCompare = strcmp((string) ($a['fecsol'] ?? ''), (string) ($b['fecsol'] ?? ''));

            return $fechaCompare !== 0 ? $fechaCompare : ((int) $a['id'] <=> (int) $b['id']);
        });

        return $dataset;
    }

    /**
     * @return array{total: int, en_termino: int, vencido: int, en_tramite: int}
     */
    public function buildResumen(array $filtros = []): array
    {
        $dataset = $this->buildDataset($filtros);

        $resumen = [
            'total' => count($dataset),
            'en_termino' => 0,
            'vencido' => 0,
            'en_tramite' => 0,
        ];

        foreach ($dataset as $row) {
            $estado = $row['estado_oportunidad'] ?? '';
            match ($estado) {
                'EN_TERMINO' => $resumen['en_termino']++,
                'VENCIDO' => $resumen['vencido']++,
                'EN_TRAMITE' => $resumen['en_tramite']++,
                default => null,
            };
        }

        return $resumen;
    }

    /**
     * Aplica filtros de documento según el tipo (doc_field del config).
     *
     * @param  array<string, mixed>  $config
     */
    private function aplicarFiltrosDocumento(
        Builder $query,
        array $config,
        string $nit,
        string $cedtra,
        string $cedcon,
        string $numdoc
    ): void {
        $docField = (string) ($config['doc_field'] ?? '');

        if ($nit !== '' && $docField === 'nit') {
            $query->where('nit', $nit);
        }

        if ($cedtra !== '' && $docField === 'cedtra') {
            $query->where('cedtra', $cedtra);
        }

        if ($cedcon !== '' && $docField === 'cedcon') {
            $query->where('cedcon', $cedcon);
        }

        if ($numdoc !== '' && $docField === 'numdoc') {
            $query->where('numdoc', $numdoc);
        }
    }

    /**
     * @return array<string, string>
     */
    private function buildTitularesIndex(): array
    {
        return Mercurio31::query()
            ->select(['cedtra', 'priape', 'segape', 'prinom', 'segnom'])
            ->get()
            ->mapWithKeys(function (Mercurio31 $trabajador): array {
                $cedtra = trim((string) $trabajador->getCedtra());
                if ($cedtra === '') {
                    return [];
                }

                $nombre = trim(implode(' ', array_filter([
                    $trabajador->getPriape(),
                    $trabajador->getSegape(),
                    $trabajador->getPrinom(),
                    $trabajador->getSegnom(),
                ])));

                return [$cedtra => $nombre];
            })
            ->all();
    }

    private function resolverEstadoOportunidad(?string $fecapr, ?int $diasHabiles, int $umbral): string
    {
        if ($diasHabiles === null) {
            return 'EN_TRAMITE';
        }

        if ($fecapr === null || $fecapr === '' || $fecapr === '0000-00-00') {
            return $diasHabiles > $umbral ? 'VENCIDO' : 'EN_TRAMITE';
        }

        return $diasHabiles > $umbral ? 'VENCIDO' : 'EN_TERMINO';
    }

    /**
     * @param  array<int|string>|string|int|null  $tipafis
     * @return array<int, array<string, mixed>>
     */
    private function resolveTipos(array|string|int|null $tipafis): array
    {
        $tipos = config('reportes.oportunidad_tipos', []);

        if ($tipafis === null || $tipafis === [] || $tipafis === '') {
            return $tipos;
        }

        if (is_int($tipafis) || is_string($tipafis)) {
            $tipafis = array_filter(array_map('trim', explode(',', (string) $tipafis)), static fn ($v) => $v !== '');
        }

        $selected = [];
        foreach ($tipafis as $tipopc) {
            $key = (int) $tipopc;
            if (isset($tipos[$key])) {
                $selected[$key] = $tipos[$key];
            }
        }

        return $selected !== [] ? $selected : $tipos;
    }
}
