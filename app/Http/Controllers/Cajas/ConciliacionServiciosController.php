<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Services\Ecommerce\ConciliacionServiciosService;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Conciliación administrativa: precompras PA vs ventas en Subsidio (ref_payco).
 */
class ConciliacionServiciosController extends ApplicationController
{
    protected int $cantidad_pagina = 15;

    public function index()
    {
        $hoy = now()->toDateString();

        return view('cajas.conciliacion_servicios.index', [
            'title' => 'Conciliación servicios vs Subsidio',
            'fecha_inicio' => now()->startOfMonth()->toDateString(),
            'fecha_fin' => $hoy,
            'filtro_estados' => ConciliacionServiciosService::ESTADOS,
        ]);
    }

    public function buscar(Request $request, ConciliacionServiciosService $service)
    {
        try {
            $data = $request->validate([
                'fecha_inicio' => 'required|date_format:Y-m-d',
                'fecha_fin' => 'required|date_format:Y-m-d|after_or_equal:fecha_inicio',
                'estado' => 'nullable|string',
                'pagina' => 'nullable|integer|min:1',
                'numero' => 'nullable|integer|min:1|max:200',
            ]);

            $pagina = (int) ($data['pagina'] ?? 1);
            if (! empty($data['numero'])) {
                $this->cantidad_pagina = (int) $data['numero'];
            }

            $resultado = $service->conciliar($data['fecha_inicio'], $data['fecha_fin']);
            $filas = $this->filtrarPorEstado($resultado['filas'], $data['estado'] ?? '');
            $paginate = Paginate::execute($filas, $pagina, $this->cantidad_pagina);
            $consultasOldServices = new GeneralService;

            $html = view('cajas.conciliacion_servicios._tabla', [
                'paginate' => $paginate,
            ])->render();

            $resumenHtml = view('cajas.conciliacion_servicios._resumen', [
                'resumen' => $resultado['resumen'],
            ])->render();

            return $this->renderObject([
                'consulta' => $html,
                'paginate' => $consultasOldServices->showPaginate($paginate, $this->cantidad_pagina),
                'resumen' => $resumenHtml,
                'totales' => $resultado['resumen'],
                'total_filas' => count($filas),
            ], false);
        } catch (ValidationException $e) {
            return $this->renderObject([
                'success' => false,
                'message' => 'Fechas inválidas',
                'errors' => $e->errors(),
            ], false);
        } catch (DebugException $e) {
            return $this->renderObject([
                'success' => false,
                'message' => $e->getMessage(),
                'consulta' => '<div class="alert alert-danger mb-0">' . e($e->getMessage()) . '</div>',
                'paginate' => '',
                'resumen' => '',
            ], false);
        } catch (\Throwable $e) {
            return $this->renderObject([
                'success' => false,
                'message' => 'Error al conciliar: ' . $e->getMessage(),
                'consulta' => '<div class="alert alert-danger mb-0">Error al conciliar: ' . e($e->getMessage()) . '</div>',
                'paginate' => '',
                'resumen' => '',
            ], false);
        }
    }

    public function reporte(Request $request, ConciliacionServiciosService $service, $format = 'csv')
    {
        try {
            $data = $request->validate([
                'fecha_inicio' => 'required|date_format:Y-m-d',
                'fecha_fin' => 'required|date_format:Y-m-d|after_or_equal:fecha_inicio',
                'estado' => 'nullable|string',
            ]);

            $resultado = $service->conciliar($data['fecha_inicio'], $data['fecha_fin']);
            $filas = $this->filtrarPorEstado($resultado['filas'], $data['estado'] ?? '');

            $filename = 'conciliacion_servicios_' . date('Ymd_His') . '.csv';

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
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se pudo generar el reporte: ' . $e->getMessage());

            return $this->renderObject($response, false);
        } catch (ValidationException $e) {
            $response = parent::errorFunc('Fechas inválidas para el reporte');

            return $this->renderObject($response, false);
        }
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
}
