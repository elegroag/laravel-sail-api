<?php

namespace App\Services\Reports;

use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Support\AfiliacionNormalizer;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ConsultaDocumentoSolicitudService
{
    /**
     * Fuentes a consultar por documento de identificación de la persona.
     *
     * @var array<int, array{model: class-string, tipopc: int, label: string, campo: string, tabla: string}>
     */
    private const FUENTES = [
        [
            'model' => Mercurio30::class,
            'tipopc' => 2,
            'label' => 'Empresa (representante)',
            'campo' => 'cedrep',
            'tabla' => 'mercurio30',
        ],
        [
            'model' => Mercurio31::class,
            'tipopc' => 1,
            'label' => 'Trabajador',
            'campo' => 'cedtra',
            'tabla' => 'mercurio31',
        ],
        [
            'model' => Mercurio32::class,
            'tipopc' => 3,
            'label' => 'Cónyuge',
            'campo' => 'cedcon',
            'tabla' => 'mercurio32',
        ],
        [
            'model' => Mercurio34::class,
            'tipopc' => 4,
            'label' => 'Beneficiario',
            'campo' => 'numdoc',
            'tabla' => 'mercurio34',
        ],
        [
            'model' => Mercurio36::class,
            'tipopc' => 10,
            'label' => 'Facultativo',
            'campo' => 'cedtra',
            'tabla' => 'mercurio36',
        ],
        [
            'model' => Mercurio38::class,
            'tipopc' => 9,
            'label' => 'Pensionado',
            'campo' => 'cedtra',
            'tabla' => 'mercurio38',
        ],
        [
            'model' => Mercurio41::class,
            'tipopc' => 13,
            'label' => 'Independiente',
            'campo' => 'cedtra',
            'tabla' => 'mercurio41',
        ],
    ];

    /**
     * @param  array{documento: string}  $filtros
     * @return array{documento: string, total: int, grupos: array<int, array<string, mixed>>}
     */
    public function consultar(array $filtros): array
    {
        $documento = trim((string) ($filtros['documento'] ?? ''));
        $grupos = [];
        $total = 0;

        foreach (self::FUENTES as $fuente) {
            $solicitudes = $this->buscarEnFuente($fuente, $documento);
            if ($solicitudes->isEmpty()) {
                continue;
            }

            $grupos[] = [
                'tipopc' => $fuente['tipopc'],
                'label' => $fuente['label'],
                'tabla' => $fuente['tabla'],
                'campo' => $fuente['campo'],
                'total' => $solicitudes->count(),
                'solicitudes' => $solicitudes->all(),
            ];
            $total += $solicitudes->count();
        }

        return [
            'documento' => $documento,
            'total' => $total,
            'grupos' => $grupos,
        ];
    }

    /**
     * @param  array{model: class-string, tipopc: int, label: string, campo: string, tabla: string}  $fuente
     * @return Collection<int, array<string, mixed>>
     */
    private function buscarEnFuente(array $fuente, string $documento): Collection
    {
        $campo = $fuente['campo'];
        $tipopc = $fuente['tipopc'];

        $rows = $fuente['model']::query()
            ->where($campo, $documento)
            ->orderByDesc('fecsol')
            ->orderByDesc('id')
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $ids = $rows->pluck('id')->map(fn ($id) => (string) $id)->all();
        $fecsisMap = $this->ultimasFecsis($tipopc, $ids);

        return $rows->map(function ($model) use ($fuente, $tipopc, $fecsisMap) {
            $normalized = AfiliacionNormalizer::normalize($model, $tipopc, [
                'label' => $fuente['label'],
            ]);

            $id = (string) ($model->id ?? '');
            $fecsis = $fecsisMap[$id] ?? null;
            $fecest = $this->formatFecha($model->fecest ?? null);

            return [
                'id' => $model->id,
                'tipopc' => $tipopc,
                'tipo' => $fuente['label'],
                'tabla' => $fuente['tabla'],
                'campo' => $fuente['campo'],
                'ruuid' => $normalized['ruuid'] ?? null,
                'nombre' => $normalized['nombre'] ?? '',
                'documento' => $normalized['documento'] ?? '',
                'fecsol' => $normalized['fecsol'] ?? null,
                'fecsis' => $fecsis ?? $fecest,
                'fecest' => $fecest,
                'estado' => $normalized['estado_codigo'] ?? ($model->estado ?? ''),
                'estado_detalle' => $normalized['estado'] ?? '',
            ];
        });
    }

    /**
     * Última fecsis de mercurio10 por número de solicitud.
     *
     * @param  array<int, string>  $numeros
     * @return array<string, string|null>
     */
    private function ultimasFecsis(int $tipopc, array $numeros): array
    {
        if ($numeros === []) {
            return [];
        }

        $rows = Mercurio10::query()
            ->where('tipopc', (string) $tipopc)
            ->whereIn('numero', $numeros)
            ->orderByDesc('fecsis')
            ->orderByDesc('item')
            ->get(['numero', 'fecsis']);

        $map = [];
        foreach ($rows as $row) {
            $key = (string) $row->numero;
            if (isset($map[$key])) {
                continue;
            }
            $map[$key] = $this->formatFecha($row->fecsis);
        }

        return $map;
    }

    private function formatFecha(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<int, string>
     */
    public static function tipopcLabels(): array
    {
        $labels = [];
        foreach (self::FUENTES as $fuente) {
            $labels[$fuente['tipopc']] = $fuente['label'];
        }

        return $labels;
    }
}
