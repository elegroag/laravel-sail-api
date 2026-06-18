<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Http\Requests\Cajas\ReporteOportunidadAfiliacionRequest;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio09;
use App\Models\Mercurio11;
use App\Services\Reports\OportunidadAfiliacionExcelExporter;
use App\Services\Reports\OportunidadAfiliacionService;
use App\Support\TrabajadorTitularResolver;
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
            'estados' => Mercurio11::all(),
        ]);
    }

    public function exportarPorAportante(ReporteOportunidadAfiliacionRequest $request): StreamedResponse
    {
        $dataset = $this->oportunidadAfiliacionService->buildDatasetGroupedByAportante($request->filtros());

        return OportunidadAfiliacionExcelExporter::stream(
            $this->baseHeaders(),
            $this->mapBaseRows($dataset),
            $this->buildFilename('aportante')
        );
    }

    public function exportarPorTrabajador(ReporteOportunidadAfiliacionRequest $request): StreamedResponse
    {
        $dataset = $this->oportunidadAfiliacionService->buildDataset($request->filtros());
        $titulares = TrabajadorTitularResolver::buildIndex(
            array_values(array_filter($dataset, fn(array $row): bool => (int) $row['tipopc'] === 1))
        );

        return OportunidadAfiliacionExcelExporter::stream(
            $this->trabajadorHeaders(),
            $this->mapTrabajadorRows($dataset, $titulares),
            $this->buildFilename('trabajador')
        );
    }

    /**
     * @return array<int, string>
     */
    private function baseHeaders(): array
    {
        return [
            'FECHA DE LA SOLICITUD DE AFILIACIÓN',
            'FECHA DE REGISTRO EN EL SISTEMA SISU',
            'FECHA DE AFILIACIÓN',
            'NÚMERO CONSECUTIVO ASIGNADO',
            'TIPO Y No. DE IDENTIFICACIÓN',
            'RAZÓN SOCIAL O NOMBRE DE LA EMPRESA',
            'APELLIDOS Y NOMBRES',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function trabajadorHeaders(): array
    {
        return array_merge($this->baseHeaders(), [
            'TIPO AFILIACIÓN',
            'TRABAJADOR TITULAR',
            'ESTADO',
            'DÍAS VENCIDOS',
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $dataset
     * @return array<int, array<int, mixed>>
     */
    private function mapBaseRows(array $dataset): array
    {
        return array_map(fn(array $row): array => $this->mapBaseRow($row), $dataset);
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<int, mixed>
     */
    private function mapBaseRow(array $row): array
    {
        return [
            $row['fecsol'],
            $row['sat_fecapr'],
            $row['fecapr'],
            $row['id'],
            $row['tipo_identificacion'],
            $row['razsoc'] ?: $row['nit'],
            $row['nombre'],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $dataset
     * @return array<int, array<int, mixed>>
     */
    private function mapTrabajadorRows(array $dataset, array $titulares): array
    {
        return array_map(function (array $row) use ($titulares): array {
            $titular = in_array((int) $row['tipopc'], [3, 4], true)
                ? TrabajadorTitularResolver::resolve((string) ($row['cedtra'] ?? ''), $titulares)
                : '';

            return array_merge($this->mapBaseRow($row), [
                $row['label'],
                $titular,
                $row['estado'],
                $row['dias_vencidos'],
            ]);
        }, $dataset);
    }

    private function buildFilename(string $modalidad): string
    {
        return 'control_oportunidad_afiliacion_' . $modalidad . '_' . now()->format('Ymd_His') . '.xlsx';
    }
}
