<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Http\Requests\Cajas\ReporteSolicitudesEmpresaRequest;
use App\Models\Gener18;
use App\Services\Reports\ReporteSolicitudesEmpresaService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReporteSolicitudesEmpresaController extends ApplicationController
{
    public function __construct(
        protected ReporteSolicitudesEmpresaService $reporteSolicitudesEmpresaService
    ) {}

    public function index(): View
    {
        $tiposDocumento = Gener18::query()
            ->orderBy('coddoc')
            ->get(['coddoc', 'detdoc', 'codrua']);

        return view('cajas.reporte_solicitudes_empresa.index', [
            'title' => 'Solicitudes por empresa aportante',
            'tiposDocumento' => $tiposDocumento,
            'tipopcLabels' => ReporteSolicitudesEmpresaService::tipopcLabels(),
            'estadosLabels' => ReporteSolicitudesEmpresaService::estadosLabels(),
        ]);
    }

    public function consultar(ReporteSolicitudesEmpresaRequest $request): JsonResponse
    {
        $resultado = $this->reporteSolicitudesEmpresaService->consultar($request->filtros());

        return response()->json($resultado);
    }
}
