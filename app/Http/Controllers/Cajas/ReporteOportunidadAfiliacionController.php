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
            'mercurio09' => Mercurio09::whereIn('tipopc', ['1', '2', '3', '4', '5', '6', '8', '9', '10', '13', '14'])->get(),
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
     * Cónyuges, beneficiarios e independientes no tienen NIT / razón social aportante.
     *
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

        // Solo un tipo y es cónyuge (3), beneficiario (4) o independiente (13).
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
