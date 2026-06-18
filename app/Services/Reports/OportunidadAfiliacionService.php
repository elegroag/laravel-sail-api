<?php

namespace App\Services\Reports;

use App\Services\Utils\CalculatorDias;
use App\Support\AfiliacionNormalizer;
use Illuminate\Database\Eloquent\Builder;

class OportunidadAfiliacionService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildDataset(array $filtros = []): array
    {
        $tipos = $this->resolveTipos($filtros['tipafis'] ?? null);
        $estados = $this->normalizeEstados($filtros['estado'] ?? null);
        $campoFecha = $filtros['campo_fecha'] ?? 'fecsol';
        $fecini = $filtros['fecini'] ?? null;
        $fecfin = $filtros['fecfin'] ?? null;
        $nit = isset($filtros['nit']) ? trim((string) $filtros['nit']) : '';
        $cedtra = isset($filtros['cedtra']) ? trim((string) $filtros['cedtra']) : '';

        $dataset = [];

        foreach ($tipos as $tipopc => $config) {
            $query = $config['model']::query();
            $dateField = $this->resolveDateField($campoFecha, $config);

            if ($fecini && $fecfin) {
                $query->whereBetween($dateField, [$fecini, $fecfin]);
            }

            if (! empty($estados)) {
                $query->whereIn('estado', $estados);
            }

            if ($nit !== '') {
                $query->where('nit', $nit);
            }

            if ($cedtra !== '') {
                $query->where(function (Builder $builder) use ($cedtra): void {
                    $builder->where('cedtra', $cedtra)
                        ->orWhere('cedcon', $cedtra)
                        ->orWhere('numdoc', $cedtra)
                        ->orWhere('documento', $cedtra);
                });
            }

            foreach ($query->orderBy('nit')->orderBy('id')->cursor() as $model) {
                $record = AfiliacionNormalizer::normalize($model, (int) $tipopc, $config);
                $record['dias_vencidos'] = CalculatorDias::calcular(
                    (string) $tipopc,
                    (int) $record['id'],
                    $record['fecsol'] ?? ''
                );
                $dataset[] = $record;
            }
        }

        return $dataset;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildDatasetGroupedByAportante(array $filtros = []): array
    {
        $dataset = $this->buildDataset($filtros);

        usort($dataset, function (array $a, array $b): int {
            $nitCompare = strcmp($a['nit'] ?: $a['razsoc'], $b['nit'] ?: $b['razsoc']);
            if ($nitCompare !== 0) {
                return $nitCompare;
            }

            return strcmp($a['razsoc'], $b['razsoc']) ?: ($a['id'] <=> $b['id']);
        });

        return $dataset;
    }

    /**
     * @param  array<int|string>|string|null  $tipafis
     * @return array<int, array<string, mixed>>
     */
    private function resolveTipos(array|string|null $tipafis): array
    {
        $tipos = config('reportes.oportunidad_tipos', []);

        if ($tipafis === null || $tipafis === [] || $tipafis === '') {
            return $tipos;
        }

        if (is_string($tipafis)) {
            $tipafis = array_filter(array_map('trim', explode(',', $tipafis)));
        }

        $selected = [];
        foreach ($tipafis as $tipopc) {
            $key = (int) $tipopc;
            if (isset($tipos[$key])) {
                $selected[$key] = $tipos[$key];
            }
        }

        return $selected ?: $tipos;
    }

    /**
     * @return array<int, string>
     */
    private function normalizeEstados(mixed $estado): array
    {
        if ($estado === null || $estado === '' || $estado === []) {
            return [];
        }

        if (is_array($estado)) {
            return array_values(array_filter(array_map('strval', $estado)));
        }

        return array_values(array_filter(array_map('trim', explode(',', (string) $estado))));
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function resolveDateField(string $campoFecha, array $config): string
    {
        if ($campoFecha === 'sat_fecapr' && ! ($config['has_sat_fecapr'] ?? false)) {
            return 'fecapr';
        }

        if (! in_array($campoFecha, ['fecsol', 'sat_fecapr', 'fecapr'], true)) {
            return 'fecsol';
        }

        return $campoFecha;
    }
}
