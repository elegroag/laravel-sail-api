<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Http\Requests\Cajas\ConsultaDocumentoSolicitudRequest;
use App\Services\Reports\ConsultaDocumentoSolicitudService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ConsultaDocumentoSolicitudController extends ApplicationController
{
    public function __construct(
        protected ConsultaDocumentoSolicitudService $consultaDocumentoSolicitudService
    ) {}

    public function index(): View
    {
        return view('cajas.consulta_documento_solicitud.index', [
            'title' => 'Consulta de solicitudes por documento',
            'tipopcLabels' => ConsultaDocumentoSolicitudService::tipopcLabels(),
        ]);
    }

    public function consultar(ConsultaDocumentoSolicitudRequest $request): JsonResponse
    {
        $resultado = $this->consultaDocumentoSolicitudService->consultar($request->filtros());

        return response()->json($resultado);
    }
}
