<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Http\Requests\Cajas\ReporteComprasServiciosRequest;
use App\Services\Reports\OportunidadAfiliacionExcelExporter;
use App\Services\Reports\ReporteComprasServiciosService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
