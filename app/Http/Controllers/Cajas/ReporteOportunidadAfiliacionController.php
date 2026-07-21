<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Http\Requests\Cajas\ReporteOportunidadAfiliacionRequest;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio09;
use App\Services\Reports\OportunidadAfiliacionExcelExporter;
use App\Services\Reports\OportunidadAfiliacionService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteOportunidadAfiliacionController extends ApplicationController
{
    protected ?DbBase $db;

    protected ?array $user;

    protected ?string $tipfun;

    public function __construct(
        protected OportunidadAfiliacionService $oportunidadAfiliacionService
    ) {
        $this->db = DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipfun = session('tipfun') ?? null;
    }

    public function index()
    {
        return view('cajas.reporte_oportunidad.index', [
            'title' => 'Reporte Oportunidad Afiliaciones',
            'mercurio09' => Mercurio09::whereIn('tipopc', ['1', '2', '3', '4', '9', '10', '11'])->get(),
            'umbralDias' => (int) config('reportes.oportunidad_umbral_dias', 3),
        ]);
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
        $dataset = $this->oportunidadAfiliacionService->buildDataset($request->filtros());

        return OportunidadAfiliacionExcelExporter::stream(
            $this->headers(),
            $this->mapRows($dataset),
            $this->buildFilename()
        );
    }

    /**
     * @return array<int, string>
     */
    private function headers(): array
    {
        return [
            '# Solicitud',
            'Tipo de afiliacion',
            'Estado',
            'Fecha de solicitud',
            'Fecha de aprobacion',
            'Dias habiles tramite',
            'Estado oportunidad',
            'NIT aportante',
            'Razon social aportante',
            'Cedula titular',
            'Trabajador titular',
            'Tipo y No. identificacion',
            'Nombres y apellidos',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $dataset
     * @return array<int, array<int, mixed>>
     */
    private function mapRows(array $dataset): array
    {
        return array_map(fn (array $row): array => [
            $row['id'],
            $row['label'],
            $row['estado'],
            $row['fecsol'],
            $row['fecha_cierre'],
            $row['dias_habiles'],
            $row['estado_oportunidad'],
            $row['nit'],
            $row['razsoc'],
            $row['cedtra_titular'],
            $row['nombre_titular'],
            $row['tipo_identificacion'],
            $row['nombre'],
        ], $dataset);
    }

    private function buildFilename(): string
    {
        return 'control_oportunidad_afiliacion_'.now()->format('Ymd_His').'.xlsx';
    }
}
