<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Controller;
use App\Services\Ecommerce\ConciliacionServiciosService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Conciliación administrativa: precompras PA vs ventas en Subsidio (ref_payco).
 */
class ConciliacionServiciosController extends Controller
{
    public function index(Request $request, ConciliacionServiciosService $service): Response
    {
        $hoy = now()->toDateString();
        $defaults = [
            'fecha_inicio' => now()->startOfMonth()->toDateString(),
            'fecha_fin' => $hoy,
            'estado' => '',
            'per_page' => 15,
        ];

        $queried = $request->boolean('queried');

        $filters = [
            'fecha_inicio' => (string) $request->query('fecha_inicio', $defaults['fecha_inicio']),
            'fecha_fin' => (string) $request->query('fecha_fin', $defaults['fecha_fin']),
            'estado' => (string) $request->query('estado', $defaults['estado']),
            'per_page' => max(1, min(200, (int) $request->query('per_page', $defaults['per_page']))),
        ];

        $estadoOptions = collect(ConciliacionServiciosService::ESTADOS)
            ->map(fn (string $label, string $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();

        $emptyResumen = collect(array_keys(ConciliacionServiciosService::ESTADOS))
            ->mapWithKeys(fn (string $codigo) => [$codigo => 0])
            ->all();

        if (! $queried) {
            return Inertia::render('Cajas/ConciliacionServicios/Index', [
                'filters' => $filters,
                'estadoOptions' => $estadoOptions,
                'estadoLabels' => ConciliacionServiciosService::ESTADOS,
                'resumen' => $emptyResumen,
                'items' => [
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
                ],
                'queried' => false,
                'error' => null,
            ]);
        }

        try {
            $request->validate([
                'fecha_inicio' => 'required|date_format:Y-m-d',
                'fecha_fin' => 'required|date_format:Y-m-d|after_or_equal:fecha_inicio',
                'estado' => 'nullable|string',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:200',
            ]);

            $resultado = $service->conciliar($filters['fecha_inicio'], $filters['fecha_fin']);
            $filas = $this->filtrarPorEstado($resultado['filas'], $filters['estado']);
            $paginator = $this->paginateFilas($filas, $request, $filters['per_page']);

            return Inertia::render('Cajas/ConciliacionServicios/Index', [
                'filters' => $filters,
                'estadoOptions' => $estadoOptions,
                'estadoLabels' => ConciliacionServiciosService::ESTADOS,
                'resumen' => $resultado['resumen'],
                'items' => [
                    'data' => array_values($paginator->items()),
                    'meta' => [
                        'pagination' => [
                            'current_page' => $paginator->currentPage(),
                            'last_page' => $paginator->lastPage(),
                            'per_page' => $paginator->perPage(),
                            'from' => $paginator->firstItem(),
                            'to' => $paginator->lastItem(),
                            'total' => $paginator->total(),
                        ],
                    ],
                ],
                'queried' => true,
                'error' => null,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (DebugException $e) {
            return Inertia::render('Cajas/ConciliacionServicios/Index', [
                'filters' => $filters,
                'estadoOptions' => $estadoOptions,
                'estadoLabels' => ConciliacionServiciosService::ESTADOS,
                'resumen' => $emptyResumen,
                'items' => [
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
                ],
                'queried' => true,
                'error' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            return Inertia::render('Cajas/ConciliacionServicios/Index', [
                'filters' => $filters,
                'estadoOptions' => $estadoOptions,
                'estadoLabels' => ConciliacionServiciosService::ESTADOS,
                'resumen' => $emptyResumen,
                'items' => [
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
                ],
                'queried' => true,
                'error' => 'Error al conciliar: '.$e->getMessage(),
            ]);
        }
    }

    public function reporte(Request $request, ConciliacionServiciosService $service, $format = 'csv'): StreamedResponse
    {
        $data = $request->validate([
            'fecha_inicio' => 'required|date_format:Y-m-d',
            'fecha_fin' => 'required|date_format:Y-m-d|after_or_equal:fecha_inicio',
            'estado' => 'nullable|string',
        ]);

        $resultado = $service->conciliar($data['fecha_inicio'], $data['fecha_fin']);
        $filas = $this->filtrarPorEstado($resultado['filas'], $data['estado'] ?? '');
        $filename = 'conciliacion_servicios_'.date('Ymd_His').'.csv';

        return new StreamedResponse(function () use ($filas) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Estado conciliación',
                'Precompra ID',
                'Documento',
                'Beneficiario',
                'Servicio',
                'Apertura',
                'Ref ePayco',
                'Valor precompra',
                'Fecha pago',
                'Estado precompra',
                'Venta marca',
                'Venta documento',
                'Venta fecha',
                'Venta valor pago',
                'Venta valor forma pago',
            ], ';');

            foreach ($filas as $fila) {
                fputcsv($handle, [
                    $fila['estado_descripcion'],
                    $fila['precompra_id'],
                    $fila['documento'],
                    $fila['codben'],
                    $fila['codser'],
                    $fila['numero'],
                    $fila['ref_payco'],
                    $fila['valor_precompra'],
                    $fila['fecha_pago'],
                    $fila['estado_precompra_desc'] ?? $fila['estado_precompra'],
                    $fila['venta_marca'],
                    $fila['venta_documento'],
                    $fila['venta_fecha'],
                    $fila['venta_valpago'],
                    $fila['venta_valor_forma_pago'],
                ], ';');
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * @param  list<array<string, mixed>>  $filas
     * @return list<array<string, mixed>>
     */
    protected function filtrarPorEstado(array $filas, string $estado): array
    {
        $estado = trim($estado);
        if ($estado === '' || ! array_key_exists($estado, ConciliacionServiciosService::ESTADOS)) {
            return $filas;
        }

        return array_values(array_filter(
            $filas,
            fn (array $fila) => ($fila['estado'] ?? '') === $estado
        ));
    }

    /**
     * @param  list<array<string, mixed>>  $filas
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    protected function paginateFilas(array $filas, Request $request, int $perPage): LengthAwarePaginator
    {
        $page = max(1, (int) $request->query('page', 1));
        $total = count($filas);
        $offset = ($page - 1) * $perPage;
        $slice = array_slice($filas, $offset, $perPage);

        return new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
