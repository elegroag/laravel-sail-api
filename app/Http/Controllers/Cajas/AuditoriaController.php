<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Services\Cajas\AuditoriaService;
use App\Services\ReportGenerator\Products\OptimizedXlsxProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditoriaController extends Controller
{
    public function __construct(
        protected AuditoriaService $auditoriaService
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'tipopc' => ['nullable', 'string'],
            'fecini' => ['nullable', 'date_format:Y-m-d'],
            'fecfin' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $tipopc = (string) ($filters['tipopc'] ?? '');
        $fecini = (string) ($filters['fecini'] ?? '');
        $fecfin = (string) ($filters['fecfin'] ?? '');

        $ready = $tipopc !== '' && $fecini !== '' && $fecfin !== '';
        $consulta = $ready
            ? $this->auditoriaService->consultar($tipopc, $fecini, $fecfin)
            : ['data' => [], 'hasExtra' => false, 'total' => 0, 'columns' => []];

        return Inertia::render('Cajas/Auditoria/Index', [
            'tipopcOptions' => $this->auditoriaService->tipopcOptions(),
            'filters' => [
                'tipopc' => $tipopc,
                'fecini' => $fecini,
                'fecfin' => $fecfin,
            ],
            'result' => $consulta,
            'queried' => $ready,
        ]);
    }

    public function show(string $tipopc, int $id): Response
    {
        try {
            $detalle = $this->auditoriaService->detalle($tipopc, $id);
        } catch (ValidationException) {
            abort(404);
        }

        return Inertia::render('Cajas/Auditoria/Show', [
            'detalle' => $detalle,
            'backFilters' => request()->only(['fecini', 'fecfin']),
        ]);
    }

    public function reporteAuditoria(Request $request): StreamedResponse|HttpResponse
    {
        return $this->streamExcel($request, 'reporte_auditoria_'.now()->format('Ymd').'.xlsx');
    }

    public function exportarExcel(Request $request): StreamedResponse|HttpResponse
    {
        return $this->streamExcel($request, 'auditoria_export_'.now()->format('Ymd_His').'.xlsx');
    }

    private function streamExcel(Request $request, string $filename): StreamedResponse|HttpResponse
    {
        $data = $request->validate([
            'tipopc' => ['required', 'string'],
            'fecini' => ['required', 'date_format:Y-m-d'],
            'fecfin' => ['required', 'date_format:Y-m-d'],
        ]);

        $reporte = $this->auditoriaService->buildReporteRows(
            $data['tipopc'],
            $data['fecini'],
            $data['fecfin'],
        );

        return OptimizedXlsxProduct::streamFromArray($reporte['headers'], $reporte['rows'], $filename);
    }
}
