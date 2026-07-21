<?php

namespace App\Services\Reports;

use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Support\AfiliacionNormalizer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ReporteSolicitudesEmpresaService
{
    public const DEFAULT_PER_PAGE = 25;

    public const MAX_PER_PAGE = 100;

    public const CACHE_TTL_MINUTES = 30;

    /**
     * @var array<int, array{model: class-string, label: string}>
     */
    private const TIPOS = [
        1 => ['model' => Mercurio31::class, 'label' => 'Trabajador'],
        3 => ['model' => Mercurio32::class, 'label' => 'Cónyuge'],
        4 => ['model' => Mercurio34::class, 'label' => 'Beneficiario'],
    ];

    /**
     * @param  array{documento: string, coddoc: string, fecini: string, fecfin: string, tipopcs: array<int, int|string>, estado?: string|null, page?: int, per_page?: int, refresh?: bool}  $filtros
     * @return array{empresa: array<string, string>, total: int, rows: array<int, array<string, mixed>>, pagination: array<string, mixed>, from_cache: bool}
     */
    public function consultar(array $filtros): array
    {
        $page = max(1, (int) ($filtros['page'] ?? 1));
        $perPage = (int) ($filtros['per_page'] ?? self::DEFAULT_PER_PAGE);
        $perPage = max(10, min(self::MAX_PER_PAGE, $perPage));
        $refresh = (bool) ($filtros['refresh'] ?? false);

        $cacheKey = $this->cacheKey($filtros);
        $fromCache = false;

        if ($refresh || ! Cache::has($cacheKey)) {
            $payload = $this->buildDataset($filtros);
            Cache::put($cacheKey, $payload, now()->addMinutes(self::CACHE_TTL_MINUTES));
        } else {
            $payload = Cache::get($cacheKey);
            $fromCache = true;

            if (! is_array($payload) || ! isset($payload['rows'], $payload['empresa'])) {
                $payload = $this->buildDataset($filtros);
                Cache::put($cacheKey, $payload, now()->addMinutes(self::CACHE_TTL_MINUTES));
                $fromCache = false;
            }
        }

        /** @var array<int, array<string, mixed>> $allRows */
        $allRows = $payload['rows'];
        $total = count($allRows);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $perPage;
        $pageRows = array_slice($allRows, $offset, $perPage);
        $from = $total === 0 ? 0 : $offset + 1;
        $to = $total === 0 ? 0 : min($offset + $perPage, $total);

        return [
            'empresa' => $payload['empresa'],
            'total' => $total,
            'rows' => array_values($pageRows),
            'from_cache' => $fromCache,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'last_page' => $lastPage,
                'from' => $from,
                'to' => $to,
                'total' => $total,
                'cache_key' => $cacheKey,
            ],
        ];
    }

    /**
     * @param  array{documento: string, coddoc: string, fecini: string, fecfin: string, tipopcs: array<int, int|string>, estado?: string|null}  $filtros
     * @return array{empresa: array<string, string>, rows: array<int, array<string, mixed>>}
     */
    private function buildDataset(array $filtros): array
    {
        $documento = trim((string) ($filtros['documento'] ?? ''));
        $coddoc = trim((string) ($filtros['coddoc'] ?? ''));
        $fecini = (string) ($filtros['fecini'] ?? '');
        $fecfin = (string) ($filtros['fecfin'] ?? '');
        $estado = isset($filtros['estado']) ? trim((string) $filtros['estado']) : '';
        $tipopcs = array_values(array_unique(array_map('intval', $filtros['tipopcs'] ?? [])));

        $rows = [];
        foreach ($tipopcs as $tipopc) {
            if (! isset(self::TIPOS[$tipopc])) {
                continue;
            }

            $config = self::TIPOS[$tipopc];
            $query = $config['model']::query()
                ->where('tipo', 'E')
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->whereBetween('fecsol', [
                    $fecini.' 00:00:00',
                    Carbon::parse($fecfin)->endOfDay()->format('Y-m-d H:i:s'),
                ]);

            if ($estado !== '') {
                $query->where('estado', $estado);
            }

            $query->orderBy('fecsol')->orderBy('id');

            foreach ($query->cursor() as $model) {
                $rows[] = $this->normalizeRow($model, $tipopc, $config['label']);
            }
        }

        usort($rows, function (array $a, array $b): int {
            $fechaCompare = strcmp((string) ($a['fecsol'] ?? ''), (string) ($b['fecsol'] ?? ''));

            return $fechaCompare !== 0 ? $fechaCompare : ((int) ($a['id'] ?? 0) <=> (int) ($b['id'] ?? 0));
        });

        return [
            'empresa' => [
                'documento' => $documento,
                'coddoc' => $coddoc,
                'tipo' => 'E',
            ],
            'rows' => $rows,
        ];
    }

    /**
     * @param  array<string, mixed>  $filtros
     */
    public function cacheKey(array $filtros): string
    {
        $userKey = 'anon';
        try {
            $user = session('user');
            if (is_array($user)) {
                $userKey = (string) ($user['usuario'] ?? $user['id'] ?? 'anon');
            } elseif (session()->isStarted()) {
                $userKey = (string) (session()->getId() ?: 'anon');
            }
        } catch (\Throwable) {
            $userKey = 'anon';
        }

        $tipopcs = array_values(array_unique(array_map('intval', $filtros['tipopcs'] ?? [])));
        sort($tipopcs);

        $fingerprint = [
            'documento' => trim((string) ($filtros['documento'] ?? '')),
            'coddoc' => trim((string) ($filtros['coddoc'] ?? '')),
            'fecini' => (string) ($filtros['fecini'] ?? ''),
            'fecfin' => (string) ($filtros['fecfin'] ?? ''),
            'estado' => isset($filtros['estado']) ? trim((string) $filtros['estado']) : '',
            'tipopcs' => $tipopcs,
        ];

        return 'rse:'.$userKey.':'.sha1(json_encode($fingerprint));
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeRow(object $model, int $tipopc, string $label): array
    {
        $config = [
            'label' => strtoupper($label),
            'doc_field' => match ($tipopc) {
                3 => 'cedcon',
                4 => 'numdoc',
                default => 'cedtra',
            },
            'afiliacion_field' => 'fecapr',
            'titular_field' => in_array($tipopc, [3, 4], true) ? 'cedtra' : null,
        ];

        $normalized = AfiliacionNormalizer::normalize($model, $tipopc, $config, []);

        return [
            'tipopc' => $tipopc,
            'tipo_label' => $label,
            'id' => $normalized['id'] ?? null,
            'ruuid' => $normalized['ruuid'] ?? '',
            'documento_afiliado' => $normalized['documento'] ?? '',
            'nombre' => $normalized['nombre'] ?? '',
            'nit' => $normalized['nit'] ?? '',
            'razsoc' => $normalized['razsoc'] ?? '',
            'fecsol' => $normalized['fecsol'] ?? null,
            'estado' => $normalized['estado'] ?? '',
            'estado_codigo' => $normalized['estado_codigo'] ?? '',
            'fecha_cierre' => $normalized['fecha_cierre'] ?? ($normalized['fecapr'] ?? $normalized['fecest'] ?? null),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function tipopcLabels(): array
    {
        return [
            1 => 'Trabajador',
            3 => 'Cónyuge',
            4 => 'Beneficiario',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function estadosLabels(): array
    {
        return array_merge(
            function_exists('solicitud_estados_array') ? solicitud_estados_array() : [],
            [
                'T' => 'Temporal',
                'C' => 'Cancelado',
            ]
        );
    }
}
