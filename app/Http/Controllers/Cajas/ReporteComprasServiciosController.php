<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cajas\ReporteComprasServiciosRequest;
use App\Services\Reports\OportunidadAfiliacionExcelExporter;
use App\Services\Reports\ReporteComprasServiciosService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteComprasServiciosController extends Controller
{
    public function __construct(
        protected ReporteComprasServiciosService $reporteComprasServiciosService
    ) {}

    public function index(Request $request): Response
    {
        $hoy = now()->toDateString();
        $defaults = [
            'fecini' => now()->startOfMonth()->toDateString(),
            'fecfin' => $hoy,
            'estado' => '',
            'per_page' => ReporteComprasServiciosService::DEFAULT_PER_PAGE,
        ];

        $queried = $request->boolean('queried');

        $filters = [
            'fecini' => (string) $request->query('fecini', $defaults['fecini']),
            'fecfin' => (string) $request->query('fecfin', $defaults['fecfin']),
            'estado' => (string) $request->query('estado', $defaults['estado']),
            'per_page' => max(
                10,
                min(
                    ReporteComprasServiciosService::MAX_PER_PAGE,
                    (int) $request->query('per_page', $defaults['per_page'])
                )
            ),
        ];

        $estadoOptions = collect(ReporteComprasServiciosService::estadosLabels())
            ->map(fn (string $label, string $value) => ['value' => $value, 'label' => "{$value} — {$label}"])
            ->values()
            ->all();

        $emptyResumen = [
            'por_estado' => collect(array_keys(ReporteComprasServiciosService::estadosLabels()))
                ->mapWithKeys(fn (string $codigo) => [$codigo => 0])
                ->all(),
            'valor_total' => 0.0,
            'valor_pagado' => 0.0,
        ];

        $emptyItems = [
            'data' => [],
            'meta' => [
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $filters['per_page'],
                    'from' => null,
                    'to' => null,
                    'total' => 0,
                ],
            ],
        ];

        if (! $queried) {
            return Inertia::render('Cajas/ReporteComprasServicios/Index', [
                'filters' => $filters,
                'estadoOptions' => $estadoOptions,
                'estadoLabels' => ReporteComprasServiciosService::estadosLabels(),
                'resumen' => $emptyResumen,
                'items' => $emptyItems,
                'queried' => false,
                'error' => null,
            ]);
        }

        try {
            $request->validate([
                'fecini' => 'required|date_format:Y-m-d',
                'fecfin' => 'required|date_format:Y-m-d|after_or_equal:fecini',
                'estado' => 'nullable|string',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:10|max:100',
            ], [
                'fecini.required' => 'La fecha inicial es obligatoria.',
                'fecfin.required' => 'La fecha final es obligatoria.',
                'fecfin.after_or_equal' => 'La fecha final debe ser mayor o igual a la fecha inicial.',
            ]);

            $resultado = $this->reporteComprasServiciosService->consultar([
                'fecini' => $filters['fecini'],
                'fecfin' => $filters['fecfin'],
                'estado' => $filters['estado'] !== '' ? $filters['estado'] : null,
                'page' => max(1, (int) $request->query('page', 1)),
                'per_page' => $filters['per_page'],
            ]);

            $pagination = $resultado['pagination'];

            return Inertia::render('Cajas/ReporteComprasServicios/Index', [
                'filters' => $filters,
                'estadoOptions' => $estadoOptions,
                'estadoLabels' => ReporteComprasServiciosService::estadosLabels(),
                'resumen' => $resultado['resumen'],
                'items' => [
                    'data' => $resultado['rows'],
                    'meta' => [
                        'pagination' => [
                            'current_page' => $pagination['page'],
                            'last_page' => $pagination['last_page'],
                            'per_page' => $pagination['per_page'],
                            'from' => $pagination['from'] ?: null,
                            'to' => $pagination['to'] ?: null,
                            'total' => $pagination['total'],
                        ],
                    ],
                ],
                'queried' => true,
                'error' => null,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return Inertia::render('Cajas/ReporteComprasServicios/Index', [
                'filters' => $filters,
                'estadoOptions' => $estadoOptions,
                'estadoLabels' => ReporteComprasServiciosService::estadosLabels(),
                'resumen' => $emptyResumen,
                'items' => $emptyItems,
                'queried' => true,
                'error' => 'Error al consultar el reporte: '.$e->getMessage(),
            ]);
        }
    }

    public function exportar(ReporteComprasServiciosRequest $request): StreamedResponse
    {
        $filtros = $request->filtros();
        $rows = $this->reporteComprasServiciosService->exportRows($filtros);

        return OportunidadAfiliacionExcelExporter::stream(
            $this->headers(),
            array_map(fn (array $row) => $this->mapRow($row), $rows),
            "ventas_en_linea_servicios_{$filtros['fecini']}_{$filtros['fecfin']}.xlsx"
        );
    }

    /**
     * @return array<int, string>
     */
    private function headers(): array
    {
        return [
            'ID',
            'Documento',
            'Beneficiario',
            'Servicio',
            'Apertura',
            'Valor',
            'Estado',
            'Ref. ePayco',
            'Transaction ID',
            'Approval code',
            'Fecha preventa',
            'Fecha pago',
            'Motivo ePayco',
            'Motivo desestimación',
            'Detalle desestimación',
            'Fecha desestimación',
            'Nota',
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<int, mixed>
     */
    private function mapRow(array $row): array
    {
        return [
            $row['id'],
            $row['documento'],
            $row['codben'],
            $row['codser'],
            $row['numero'],
            $row['valor'] !== null ? (float) $row['valor'] : null,
            $row['estado_detalle'] ?: $row['estado'],
            $row['ref_payco'],
            $row['transaction_id'],
            $row['approval_code'],
            $row['fecha_precompra'],
            $row['fecha_pago'],
            $row['motivo_epayco'],
            $row['motivo_desestimacion'],
            $row['detalle_desestimacion'],
            $row['fecha_desestimacion'],
            $row['nota'],
        ];
    }
}
