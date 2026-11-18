<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cajas\ReporteOportunidadAfiliacionRequest;
use App\Models\Mercurio09;
use App\Services\Reports\OportunidadAfiliacionExcelExporter;
use App\Services\Reports\OportunidadAfiliacionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteOportunidadAfiliacionController extends Controller
{
    private const TIPOPC_PERMITIDOS = ['1', '2', '3', '4', '5', '6', '8', '9', '10', '13', '14'];

    public function __construct(
        protected OportunidadAfiliacionService $oportunidadAfiliacionService
    ) {}

    public function index(Request $request): Response
    {
        $hoy = now()->toDateString();
        $queried = $request->boolean('queried');
        $umbralDias = (int) config('reportes.oportunidad_umbral_dias', 3);

        $filters = [
            'fecini' => (string) $request->query('fecini', now()->startOfMonth()->toDateString()),
            'fecfin' => (string) $request->query('fecfin', $hoy),
            'tipafis' => trim((string) $request->query('tipafis', '')),
        ];

        $tipafiOptions = $this->tipafiOptions();
        $emptyResumen = [
            'total' => 0,
            'en_termino' => 0,
            'vencido' => 0,
            'en_tramite' => 0,
        ];

        if (! $queried) {
            return Inertia::render('Cajas/ReporteOportunidad/Index', [
                'filters' => $filters,
                'tipafiOptions' => $tipafiOptions,
                'umbralDias' => $umbralDias,
                'resumen' => $emptyResumen,
                'queried' => false,
                'error' => null,
                'exportarUrl' => route('cajas.reporte-oportunidad.exportar'),
            ]);
        }

        try {
            $tipafisPayload = $filters['tipafis'] !== '' ? [(int) $filters['tipafis']] : null;
            $request->merge([
                'fecini' => $filters['fecini'],
                'fecfin' => $filters['fecfin'],
                'tipafis' => $tipafisPayload,
            ]);

            $validated = $request->validate([
                'fecini' => ['required', 'date_format:Y-m-d'],
                'fecfin' => ['required', 'date_format:Y-m-d', 'after_or_equal:fecini'],
                'tipafis' => ['nullable', 'array'],
                'tipafis.*' => ['integer', 'in:1,2,3,4,5,6,8,9,10,11,13,14'],
            ], [
                'fecini.required' => 'La fecha inicial es obligatoria.',
                'fecfin.required' => 'La fecha final es obligatoria.',
                'fecfin.after_or_equal' => 'La fecha final debe ser mayor o igual a la fecha inicial.',
            ]);

            $resumen = $this->oportunidadAfiliacionService->buildResumen($validated);

            return Inertia::render('Cajas/ReporteOportunidad/Index', [
                'filters' => $filters,
                'tipafiOptions' => $tipafiOptions,
                'umbralDias' => $umbralDias,
                'resumen' => $resumen,
                'queried' => true,
                'error' => null,
                'exportarUrl' => route('cajas.reporte-oportunidad.exportar'),
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return Inertia::render('Cajas/ReporteOportunidad/Index', [
                'filters' => $filters,
                'tipafiOptions' => $tipafiOptions,
                'umbralDias' => $umbralDias,
                'resumen' => $emptyResumen,
                'queried' => true,
                'error' => 'Error al consultar el resumen: '.$e->getMessage(),
                'exportarUrl' => route('cajas.reporte-oportunidad.exportar'),
            ]);
        }
    }

    public function previsualizar(ReporteOportunidadAfiliacionRequest $request): JsonResponse
    {
        return response()->json([
            'resumen' => $this->oportunidadAfiliacionService->buildResumen($request->filtros()),
            'umbral_dias' => (int) config('reportes.oportunidad_umbral_dias', 3),
        ]);
    }

    public function exportar(ReporteOportunidadAfiliacionRequest $request): StreamedResponse
    {
        $filtros = $request->filtros();
        $dataset = $this->oportunidadAfiliacionService->buildDataset($filtros);
        $incluirAportante = $this->debeIncluirCamposAportante($filtros);

        return OportunidadAfiliacionExcelExporter::stream(
            $this->headers($incluirAportante),
            $this->mapRows($dataset, $incluirAportante),
            $this->buildFilename()
        );
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function tipafiOptions(): array
    {
        $fallbacks = [
            '1' => 'Trabajador',
            '2' => 'Empresa',
            '3' => 'Cónyuge',
            '4' => 'Beneficiario',
            '5' => 'Empresa temporal',
            '6' => 'Estudiante',
            '8' => 'Actualización',
            '9' => 'Pensionado',
            '10' => 'Facultativo',
            '13' => 'Independiente',
            '14' => 'Otro',
        ];

        try {
            $rows = Mercurio09::query()
                ->whereIn('tipopc', self::TIPOPC_PERMITIDOS)
                ->orderBy('tipopc')
                ->get(['tipopc', 'detalle']);

            if ($rows->isEmpty()) {
                return collect(self::TIPOPC_PERMITIDOS)
                    ->map(fn (string $value) => [
                        'value' => $value,
                        'label' => $fallbacks[$value] ?? $value,
                    ])
                    ->values()
                    ->all();
            }

            return $rows->map(function ($row) use ($fallbacks) {
                $key = (string) $row->tipopc;
                $detalle = trim((string) ($row->detalle ?? ''));

                return [
                    'value' => $key,
                    'label' => $detalle !== '' ? $detalle : ($fallbacks[$key] ?? $key),
                ];
            })->values()->all();
        } catch (\Throwable) {
            return collect(self::TIPOPC_PERMITIDOS)
                ->map(fn (string $value) => [
                    'value' => $value,
                    'label' => $fallbacks[$value] ?? $value,
                ])
                ->values()
                ->all();
        }
    }

    /**
     * @param  array<string, mixed>  $filtros
     */
    private function debeIncluirCamposAportante(array $filtros): bool
    {
        $tipafis = $filtros['tipafis'] ?? null;

        if ($tipafis === null || $tipafis === [] || $tipafis === '') {
            return true;
        }

        if (! is_array($tipafis)) {
            $tipafis = [(int) $tipafis];
        }

        $tipafis = array_values(array_unique(array_map('intval', $tipafis)));

        return ! (count($tipafis) === 1 && in_array($tipafis[0], [3, 4, 13], true));
    }

    /**
     * @return array<int, string>
     */
    private function headers(bool $incluirAportante = true): array
    {
        $headers = [
            'RUUID',
            'Estado',
            'Estado radicado',
            'Fecha envio a caja',
            'Fecha de cierre',
            'Dias habiles tramite',
        ];

        if ($incluirAportante) {
            $headers[] = 'NIT aportante';
            $headers[] = 'Razon social aportante';
        }

        $headers[] = 'Tipo identificacion';
        $headers[] = 'No. identificacion';
        $headers[] = 'Nombres y apellidos';
        $headers[] = 'Usuario';
        $headers[] = 'Nombre usuario';

        return $headers;
    }

    /**
     * @param  array<int, array<string, mixed>>  $dataset
     * @return array<int, array<int, mixed>>
     */
    private function mapRows(array $dataset, bool $incluirAportante = true): array
    {
        return array_map(function (array $row) use ($incluirAportante): array {
            $mapped = [
                $row['ruuid'] ?? '',
                $row['estado'],
                $row['estado_radicado'] ?? '',
                $row['fecsol'],
                $row['fecha_cierre'] ?? $row['fecapr'] ?? '',
                $row['dias_habiles'],
            ];

            if ($incluirAportante) {
                $mapped[] = $row['nit'];
                $mapped[] = $row['razsoc'];
            }

            $mapped[] = $row['tipo_documento'] ?? $row['tipdoc'] ?? '';
            $mapped[] = $row['numero_identificacion'] ?? $row['documento'] ?? '';
            $mapped[] = $row['nombre'];
            $mapped[] = $row['usuario'] ?? '';
            $mapped[] = $row['nombre_usuario'] ?? '';

            return $mapped;
        }, $dataset);
    }

    private function buildFilename(): string
    {
        return 'control_oportunidad_afiliacion_'.now()->format('Ymd_His').'.xlsx';
    }
}
