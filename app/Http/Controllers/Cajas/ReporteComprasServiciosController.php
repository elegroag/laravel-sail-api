<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Http\Requests\Cajas\ReporteComprasServiciosRequest;
use App\Services\Reports\ReporteComprasServiciosService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReporteComprasServiciosController extends ApplicationController
{
    public function __construct(
        protected ReporteComprasServiciosService $reporteComprasServiciosService
    ) {}

    public function index(): View
    {
        return view('cajas.reporte_compras_servicios.index', [
            'title' => 'Ventas en línea de servicios',
            'estadosLabels' => ReporteComprasServiciosService::estadosLabels(),
        ]);
    }

    public function consultar(ReporteComprasServiciosRequest $request): JsonResponse
    {
        $resultado = $this->reporteComprasServiciosService->consultar($request->filtros());

        return response()->json($resultado);
    }
}
