<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Gener02;
use App\Models\Mercurio07;
use App\Models\Mercurio09;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio46;
use App\Services\Api\ApiSubsidio;
use App\Services\Certificados\Certificado;
use App\Services\Certificados\CertiTrabajador;
use App\Services\ReportGenerator\ReportService;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\GeneralService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConsultaController extends ApplicationController
{

    protected $user;

    protected $tipfun;

    public function __construct()
    {
        $this->user = session('user');
        $this->tipfun = session('tipfun');
    }

    public function index()
    {
        return view('cajas.consulta.index', [
            'title' => 'Consulta',
        ]);
    }

    public function cargaLaboral()
    {
        $gener02 = Gener02::select('gener02.usuario', 'gener02.nombre', 'gener02.login')
            ->join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')
            ->distinct()
            ->get();

        $generalService = new GeneralService;
        $mercurio09 = Mercurio09::select(
            'gener02.usuario',
            'gener02.nombre',
            'mercurio09.detalle',
            'mercurio09.tipopc',
            'mercurio09.dias'
        )
            ->join('mercurio08', 'mercurio09.tipopc', '=', 'mercurio08.tipopc')
            ->join('gener02', 'gener02.usuario', '=', 'mercurio08.usuario')
            ->where('gener02.estado', 'A')
            ->where('mercurio08.codofi', '01')
            ->get()
            ->map(function ($item) use ($generalService) {
                $item = $item->toArray();
                $out = $generalService->consultaTipopc(
                    $item['tipopc'],
                    'count',
                    '',
                    $item['usuario'],
                    ['estado' => 'P']
                );
                $item['cantidad'] = $out['count'];

                return $item;
            });

        return view('cajas.consulta.carga_laboral', [
            'title' => 'Carga Laboral',
            'gener02' => $gener02,
            'mercurio09' => $mercurio09,
        ]);
    }

    public function solicitudesCargaLaboral(Request $request)
    {
        $tipopc = (string) $request->input('tipopc');
        $usuario = (string) $request->input('usuario');
        $detalle = (string) $request->input('detalle', '');

        if ($tipopc === '' || $usuario === '') {
            return response()->json([
                'success' => false,
                'msj' => 'Parámetros incompletos.',
            ], 422);
        }

        try {
            $generalService = new GeneralService;
            $out = $generalService->consultaTipopc($tipopc, 'alluser', '', $usuario);
            $datos = collect($out['datos'] ?? []);

            $solicitudes = $datos->map(function ($item) use ($tipopc) {
                [$documento, $nombre] = $this->resolverDocumentoNombreCarga($tipopc, $item);
                $fecsol = $item->fecsol ?? null;
                $fecsolFmt = $fecsol
                    ? (Carbon::parse($fecsol)->format('Y-m-d'))
                    : '';

                $dias = '';
                if ($fecsol) {
                    $dias = (string) Carbon::parse($fecsol)
                        ->startOfDay()
                        ->diffInDays(now()->startOfDay());
                }

                return [
                    'id' => $item->id,
                    'documento' => $documento,
                    'nombre' => $nombre,
                    'fecsol' => $fecsolFmt,
                    'dias' => $dias,
                    'estado' => $item->estado ?? 'P',
                ];
            })->values();

            if ($detalle === '') {
                $detalle = (string) optional(Mercurio09::find($tipopc))->detalle;
            }

            $html = view('cajas.consulta._tabla_carga_laboral', [
                'solicitudes' => $solicitudes,
                'tipopc' => $tipopc,
            ])->render();

            return response()->json([
                'success' => true,
                'detalle' => $detalle,
                'count' => $solicitudes->count(),
                'html' => $html,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'msj' => $e->getMessage(),
            ], 500);
        }
    }

    private function resolverDocumentoNombreCarga(string $tipopc, $item): array
    {
        $tipopc = (string) (int) $tipopc;

        $documento = match ($tipopc) {
            '1', '7', '9', '10', '11', '12', '13' => $item->cedtra ?? $item->documento ?? '',
            '2' => $item->nit ?? $item->documento ?? '',
            '3' => $item->cedcon ?? $item->documento ?? '',
            '4' => $item->numdoc ?? $item->documento ?? '',
            '5', '6', '14' => $item->documento ?? $item->nit ?? '',
            '8' => $item->codben ?? $item->documento ?? $item->cedtra ?? '',
            default => $item->documento
                ?? $item->cedtra
                ?? $item->nit
                ?? $item->cedcon
                ?? $item->numdoc
                ?? '',
        };

        return [(string) $documento, $this->resolverNombreCarga($item, $tipopc)];
    }

    private function resolverNombreCarga($item, string $tipopc = ''): string
    {
        // Empresas / actualización empresa: mostrar razón social.
        if (in_array($tipopc, ['2', '5'], true)) {
            $razsoc = $this->resolverRazsocEmpresa($item);
            if ($razsoc !== '') {
                return $razsoc;
            }
        }

        // Actualización de datos trabajador: nombre del solicitante.
        if (in_array($tipopc, ['6', '14'], true)) {
            $nombreSolicitante = $this->resolverNombreSolicitante($item);
            if ($nombreSolicitante !== '') {
                return $nombreSolicitante;
            }
        }

        if (is_object($item) && method_exists($item, 'getNombreCompleto')) {
            $nombre = $this->normalizarNombre($item->getNombreCompleto());
            if ($nombre !== '') {
                return $nombre;
            }
        }

        if (is_object($item) && method_exists($item, 'getNombre')) {
            $nombre = $this->normalizarNombre($item->getNombre());
            if ($nombre !== '') {
                return $nombre;
            }
        }

        $partes = array_filter([
            $item->priape ?? null,
            $item->segape ?? null,
            $item->prinom ?? null,
            $item->segnom ?? null,
        ], static fn ($valor) => filled($valor));

        if ($partes !== []) {
            return $this->normalizarNombre(implode(' ', $partes));
        }

        foreach (['razsoc', 'nomtra', 'nombre'] as $campo) {
            $nombre = $this->normalizarNombre($item->{$campo} ?? '');
            if ($nombre !== '') {
                return $nombre;
            }
        }

        if (is_object($item) && method_exists($item, 'getRazsoc')) {
            $nombre = $this->normalizarNombre($item->getRazsoc());
            if ($nombre !== '') {
                return $nombre;
            }
        }

        if (is_object($item) && method_exists($item, 'getNomtra')) {
            $nombre = $this->normalizarNombre($item->getNomtra());
            if ($nombre !== '') {
                return $nombre;
            }
        }

        return '';
    }

    private function resolverRazsocEmpresa($item): string
    {
        if (is_object($item) && method_exists($item, 'getRazsoc')) {
            $nombre = $this->normalizarNombre($item->getRazsoc());
            if ($nombre !== '') {
                return $nombre;
            }
        }

        $nombre = $this->normalizarNombre($item->razsoc ?? '');
        if ($nombre !== '') {
            return $nombre;
        }

        $nit = $item->nit ?? $item->documento ?? null;
        if (filled($nit)) {
            $razsoc = Mercurio30::query()
                ->where('nit', $nit)
                ->orderByDesc('id')
                ->value('razsoc');

            $nombre = $this->normalizarNombre($razsoc ?? '');
            if ($nombre !== '') {
                return $nombre;
            }
        }

        $documento = $item->documento ?? null;
        if (filled($documento)) {
            $query = Mercurio07::query()->where('documento', $documento);
            if (filled($item->coddoc ?? null)) {
                $query->where('coddoc', $item->coddoc);
            }
            if (filled($item->tipo ?? null)) {
                $query->where('tipo', $item->tipo);
            }

            $nombre = $this->normalizarNombre($query->value('nombre') ?? '');
            if ($nombre !== '') {
                return $nombre;
            }
        }

        return '';
    }

    private function resolverNombreSolicitante($item): string
    {
        $documento = $item->documento ?? null;
        if (! filled($documento)) {
            return '';
        }

        $query = Mercurio07::query()->where('documento', $documento);
        if (filled($item->coddoc ?? null)) {
            $query->where('coddoc', $item->coddoc);
        }
        if (filled($item->tipo ?? null)) {
            $query->where('tipo', $item->tipo);
        }

        return $this->normalizarNombre($query->value('nombre') ?? '');
    }

    private function normalizarNombre(mixed $nombre): string
    {
        return trim(preg_replace('/\s+/', ' ', (string) $nombre) ?? '');
    }

    public function reporteExcelCargaLaboral(ReportService $reportService)
    {
        $fecha = new \DateTime;
        $filename = 'reporte_carga_laboral'.$fecha->format('Ymd').'.xlsx';

        $mercurio09 = Mercurio09::all();
        $gener02 = Gener02::select('gener02.usuario', 'gener02.nombre', 'gener02.login')
            ->join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')
            ->get();

        $dataGenerator = (function () use ($mercurio09, $gener02) {
            $headers = ['Usuario/Movimiento'];
            foreach ($mercurio09 as $mmercurio09) {
                $headers[] = $mmercurio09->getDetalle();
            }
            yield $headers;

            $generalService = new GeneralService;
            foreach ($gener02 as $mgener02) {
                $row = [$mgener02->getNombre()];
                foreach ($mercurio09 as $mmercurio09) {
                    $condi = ['estado' => 'P'];
                    $result = $generalService->consultaTipopc($mmercurio09->getTipopc(), 'count', null, $mgener02->getUsuario(), $condi);
                    $row[] = $result['count'];
                }
                yield $row;
            }
        })();

        return $reportService->generateAndStream('xlsx', $dataGenerator, $filename);
    }

    public function reporteExcelIndicadores($fecini, $fecfin, ReportService $reportService)
    {
        $fecha = new \DateTime;
        $filename = 'reporte_indicadores'.$fecha->format('Ymd').'.xlsx';

        $mercurio09 = Mercurio09::all();
        $estados = (new Mercurio31)->getEstadoArray();
        $gener02 = Gener02::select('gener02.usuario', 'gener02.nombre', 'gener02.login')
            ->join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')
            ->get();

        $dataGenerator = (function () use ($fecini, $fecfin, $mercurio09, $estados, $gener02) {
            // Encabezados aplanados: Usuario/Movimiento + (por cada movimiento: estados..., TOT, VEN)
            $headers = ['Usuario/Movimiento'];
            foreach ($mercurio09 as $mmercurio09) {
                foreach ($estados as $label) {
                    $headers[] = $mmercurio09->getDetalle().' - '.$label;
                }
                $headers[] = $mmercurio09->getDetalle().' - TOT';
                $headers[] = $mmercurio09->getDetalle().' - VEN';
            }
            yield $headers;

            $consultasOldServices = new GeneralService;
            foreach ($gener02 as $mgener02) {
                $row = [$mgener02->getNombre()];
                foreach ($mercurio09 as $mmercurio09) {
                    $total_estado = 0;
                    // Conteo por estado
                    foreach ($estados as $key => $label) {
                        $condi = "estado='$key' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin'";
                        $result = $consultasOldServices->consultaTipopc($mmercurio09->getTipopc(), 'count', null, $mgener02->getUsuario(), $condi);
                        $count = $result['count'];
                        $row[] = $count;
                        $total_estado += $count;
                    }

                    // Total de estados (excluye 'T' en la consulta de vencidos original, pero aquí es suma de anteriores)
                    $row[] = $total_estado;

                    // Vencidos según lógica original de este método
                    $condi = "estado<>'T' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin'";
                    $result = $consultasOldServices->consultaTipopc($mmercurio09->getTipopc(), 'count', null, $mgener02->getUsuario(), $condi);
                    $mercurio = $result['all'];
                    $total_vencido = 0;
                    foreach ($mercurio as $mmercurio) {
                        $dias_vencidos = CalculatorDias::calcular(
                            $mmercurio09->getTipopc(),
                            $mmercurio->getId()
                        );
                        if ($dias_vencidos > $mmercurio09->getDias()) {
                            $total_vencido++;
                        }
                    }
                    $row[] = $total_vencido;
                }

                yield $row;
            }
        })();

        return $reportService->generateAndStream('xlsx', $dataGenerator, $filename);
    }

    public function indicadores()
    {
        return view('cajas.consulta.indicadores', [
            'title' => 'Consulta Indicadores',
        ]);
    }

    public function consultaIndicadores(Request $request)
    {
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');
        $generalService = new GeneralService;
        $data_indicadores = Gener02::select(
            'gener02.usuario',
            'gener02.nombre',
            DB::raw('COUNT(*) as cantidad'),
            'mercurio08.tipopc',
            'mercurio09.detalle',
            'mercurio09.dias'
        )
            ->join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')
            ->join('mercurio09', 'mercurio08.tipopc', '=', 'mercurio09.tipopc')
            ->groupBy('mercurio08.tipopc', 'gener02.usuario')
            ->get()
            ->map(function ($item) use ($generalService, $fecini) {
                $item = $item->toArray();

                $condi_aprobado = "estado='A' and fecsol>='{$fecini}'";
                $result_aprobado = $generalService->consultaTipopc($item['tipopc'], 'count', null, $item['usuario'], $condi_aprobado);
                $item['estado_aprobado'] = $result_aprobado['count'];
                $mercurio_aprobado = $result_aprobado['all'];

                $total_vencido = 0;
                foreach ($mercurio_aprobado as $mmercurio) {
                    $dias_vencidos = CalculatorDias::calcular($item['tipopc'], $mmercurio->id);
                    if ($dias_vencidos > $item['dias']) {
                        $total_vencido++;
                    }
                }

                $condi_rechazo = "estado='R' and fecsol>='{$fecini}'";
                $result_rechazo = $generalService->consultaTipopc($item['tipopc'], 'count', null, $item['usuario'], $condi_rechazo);
                $item['estado_rechazo'] = $result_rechazo['count'];
                $mercurio_rechazo = $result_rechazo['all'];

                foreach ($mercurio_rechazo as $mmercurio) {
                    $dias_vencidos = CalculatorDias::calcular($item['tipopc'], $mmercurio->id);
                    if ($dias_vencidos > $item['dias']) {
                        $total_vencido++;
                    }
                }

                $condi_pendiente = "estado='P' and fecsol>='{$fecini}'";
                $result_pendiente = $generalService->consultaTipopc($item['tipopc'], 'count', null, $item['usuario'], $condi_pendiente);
                $item['estado_pendiente'] = $result_pendiente['count'];
                $mercurio_pendiente = $result_pendiente['all'];
                foreach ($mercurio_pendiente as $mmercurio) {
                    $dias_vencidos = CalculatorDias::calcular($item['tipopc'], $mmercurio->id);
                    if ($dias_vencidos > $item['dias']) {
                        $total_vencido++;
                    }
                }

                $condi_devuelto = "estado='D' and fecsol>='{$fecini}'";
                $result_devuelto = $generalService->consultaTipopc($item['tipopc'], 'count', null, $item['usuario'], $condi_devuelto);
                $item['estado_devuelto'] = $result_devuelto['count'];
                $mercurio_devuelto = $result_devuelto['all'];

                foreach ($mercurio_devuelto as $mmercurio) {
                    $dias_vencidos = CalculatorDias::calcular($item['tipopc'], $mmercurio->id);
                    if ($dias_vencidos > $item['dias']) {
                        $total_vencido++;
                    }
                }

                $item['total_vencido'] = $total_vencido;

                return $item;
            });

        $html = view(
            'cajas.consulta._tabla-indicadores',
            [
                'data_indicadores' => $data_indicadores,
            ]
        )
            ->render();

        return response()->json([
            'html' => $html,
            'success' => true,
        ]);
    }

    public function consultaActivacionMasivaView()
    {
        return view('cajas.consulta.activacion_masiva', [
            'title' => 'Consulta Activacion Masiva',
        ]);
    }

    public function consultaActivacionMasiva(Request $request)
    {
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');
        $html = '';
        $html = "<div class='table-responsive'> ";
        $html .= "<table class='table'>";
        $html .= '<tr>';
        $html .= '<td>Id</td>';
        $html .= '<td>Empresa</td>';
        $html .= '<td>Fecha Cargue</td>';
        $html .= '<td>Archivo</td>';
        $html .= '</tr>';
        $condi = " mercurio46.fecsis>='$fecini' and mercurio46.fecsis<='$fecfin' ";

        $mercurio = Mercurio46::whereRaw($condi)->get();
        foreach ($mercurio as $mmercurio) {
            $html .= '<tr>';
            $html .= "<td>{$mmercurio->getId()}</td>";
            $html .= "<td>{$mmercurio->getNit()}</td>";
            $html .= "<td>{$mmercurio->getFecsis()}</td>";
            $html .= '<td>';
            $html .= "<a href='#' onclick='descarga_activacion(this)'>".$mmercurio->getArchivo().'</a>';
            $html .= '</td>';
            $html .= '</tr>';
        }

        return $this->renderObject(['consulta' => $html], false);
    }

    public function certificadoTrabajadorView()
    {
        return view('cajas.consulta.certificado_trabajador', [
            'title' => 'Certificado para Trabajador',
            'tipos' => [
                'A' => 'Certificación afiliación principal',
                'I' => 'Certificación con núcleo',
                'T' => 'Certificación de multiafiliación',
                'P' => 'Reporte trabajador en planillas',
            ],
        ]);
    }

    public function trabajadoresPorNit(Request $request)
    {
        $nit = trim((string) $request->input('nit', ''));

        if ($nit === '') {
            return response()->json([
                'success' => false,
                'msj' => 'El NIT es obligatorio.',
            ], 422);
        }

        try {
            $trabajadores = $this->obtenerTrabajadoresPorNit($nit);

            return response()->json([
                'success' => true,
                'trabajadores' => $trabajadores,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'msj' => $e->getMessage(),
            ], 500);
        }
    }

    public function certificadoTrabajador(Request $request)
    {
        try {
            $validated = $request->validate([
                'nit' => 'required|string',
                'cedtra' => 'required|string',
                'tipo' => 'required|in:A,I,T,P',
            ]);

            $this->validarTrabajadorEmpresa($validated['nit'], $validated['cedtra']);

            $certificado = new Certificado(
                new CertiTrabajador($validated['cedtra'], $validated['tipo'])
            );
            $certificado->generate();

            $path = $certificado->getFilePath();
            if (! is_file($path)) {
                throw new DebugException('No se pudo generar el archivo del certificado.', 500);
            }

            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$certificado->getDownloadName().'"',
            ]);
        } catch (ValidationException $e) {
            $msj = collect($e->errors())->flatten()->first() ?: 'Datos inválidos.';

            return $this->respondCertificadoError($request, $msj, 422);
        } catch (\Throwable $e) {
            return $this->respondCertificadoError(
                $request,
                $e->getMessage() ?: 'No se pudo generar el certificado.',
                (int) ($e->getCode() ?: 422) ?: 422
            );
        }
    }

    private function respondCertificadoError(Request $request, string $msj, int $status = 422)
    {
        if ($request->expectsJson() || $request->ajax() || $request->headers->has('X-Requested-With')) {
            return response()->json([
                'success' => false,
                'msj' => $msj,
            ], $status >= 400 ? $status : 422);
        }

        set_flashdata('error', [
            'msj' => $msj,
            'code' => $status,
        ]);

        return redirect()->route('consulta.certificadoTrabajador');
    }

    /**
     * @return array<string, string>
     */
    private function obtenerTrabajadoresPorNit(string $nit): array
    {
        $ps = new ApiSubsidio;
        $ps->send([
            'servicio' => 'ComfacaAfilia',
            'metodo' => 'listar_trabajadores',
            'params' => ['nit' => $nit],
        ]);

        $out = $ps->toArray();

        if (! ($out['success'] ?? false)) {
            throw new DebugException($out['msj'] ?? 'No se pudo listar trabajadores.', 502);
        }

        $trabajadores = [];

        foreach ($out['data'] ?? [] as $trabajador) {
            $cedtra = (string) ($trabajador['cedtra'] ?? '');
            if ($cedtra === '') {
                continue;
            }

            $trabajadores[$cedtra] = (string) ($trabajador['nombre'] ?? $cedtra);
        }

        return $trabajadores;
    }

    private function validarTrabajadorEmpresa(string $nit, string $cedtra): void
    {
        $trabajadores = $this->obtenerTrabajadoresPorNit($nit);

        if (! array_key_exists($cedtra, $trabajadores)) {
            throw new DebugException('El trabajador no pertenece a la empresa indicada.', 422);
        }
    }
}
