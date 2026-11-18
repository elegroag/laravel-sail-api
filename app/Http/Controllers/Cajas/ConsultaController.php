<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Gener02;
use App\Models\Mercurio07;
use App\Models\Mercurio09;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
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
use Inertia\Inertia;
use Inertia\Response;

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

    public function cargaLaboral(): Response
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
                    (string) $item['tipopc'],
                    'count',
                    '',
                    (string) $item['usuario'],
                    ['estado' => 'P']
                );
                $item['cantidad'] = (int) ($out['count'] ?? 0);

                return $item;
            });

        $asesores = $gener02->map(function ($asesor) use ($mercurio09) {
            $items = collect($mercurio09)
                ->filter(fn ($m09) => (string) $m09['usuario'] === (string) $asesor->usuario)
                ->sortByDesc('cantidad')
                ->values()
                ->map(fn ($item) => [
                    'tipopc' => (string) $item['tipopc'],
                    'detalle' => (string) $item['detalle'],
                    'dias' => $item['dias'] ?? null,
                    'cantidad' => (int) $item['cantidad'],
                ])
                ->all();

            $total = array_sum(array_column($items, 'cantidad'));

            return [
                'usuario' => (string) $asesor->usuario,
                'nombre' => (string) $asesor->getNombre(),
                'login' => (string) ($asesor->login ?? $asesor->getLogin()),
                'items' => $items,
                'total' => $total,
                'tipos' => count($items),
            ];
        })
            ->sortByDesc('total')
            ->values()
            ->all();

        $totalPendientes = array_sum(array_column($asesores, 'total'));
        $asesoresConCarga = count(array_filter($asesores, fn ($asesor) => $asesor['total'] > 0));

        return Inertia::render('Cajas/Consulta/CargaLaboral', [
            'asesores' => $asesores,
            'summary' => [
                'total_asesores' => count($asesores),
                'asesores_con_carga' => $asesoresConCarga,
                'total_pendientes' => $totalPendientes,
            ],
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

            return response()->json([
                'success' => true,
                'detalle' => $detalle,
                'count' => $solicitudes->count(),
                'es_empresa' => in_array((string) (int) $tipopc, ['2', '5'], true),
                'solicitudes' => $solicitudes,
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
                    $result = $generalService->consultaTipopc((string) $mmercurio09->getTipopc(), 'count', '', (string) $mgener02->getUsuario(), $condi);
                    $row[] = $result['count'];
                }
                yield $row;
            }
        })();

        return $reportService->generateAndStream('xlsx', $dataGenerator, $filename);
    }

    public function reporteExcelIndicadores(Request $request, ReportService $reportService)
    {
        $data = $request->validate([
            'fecini' => ['required', 'date_format:Y-m-d'],
            'fecfin' => ['required', 'date_format:Y-m-d'],
        ]);

        $fecini = $data['fecini'];
        $fecfin = $data['fecfin'];

        $fecha = new \DateTime;
        $filename = 'reporte_indicadores'.$fecha->format('Ymd').'.xlsx';

        $mercurio09 = Mercurio09::all();
        $estados = (new Mercurio31)->getEstadoArray();
        $gener02 = Gener02::select('gener02.usuario', 'gener02.nombre', 'gener02.login')
            ->join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')
            ->distinct()
            ->get();

        $dataGenerator = (function () use ($fecini, $fecfin, $mercurio09, $estados, $gener02) {
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
                    foreach ($estados as $key => $label) {
                        $condi = "estado='{$key}' and mercurio20.fecha>='{$fecini}' and mercurio20.fecha<='{$fecfin}'";
                        $result = $consultasOldServices->consultaTipopc((string) $mmercurio09->getTipopc(), 'count', '', (string) $mgener02->getUsuario(), $condi);
                        $count = $result['count'];
                        $row[] = $count;
                        $total_estado += $count;
                    }

                    $row[] = $total_estado;

                    $condi = "estado<>'T' and mercurio20.fecha>='{$fecini}' and mercurio20.fecha<='{$fecfin}'";
                    $result = $consultasOldServices->consultaTipopc((string) $mmercurio09->getTipopc(), 'count', '', (string) $mgener02->getUsuario(), $condi);
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

    public function indicadores(): Response
    {
        return Inertia::render('Cajas/Consulta/Indicadores', [
            'filters' => [
                'fecini' => '',
                'fecfin' => '',
            ],
            'rows' => [],
            'queried' => false,
        ]);
    }

    public function consultaIndicadores(Request $request)
    {
        $data = $request->validate([
            'fecini' => ['required', 'date_format:Y-m-d'],
            'fecfin' => ['required', 'date_format:Y-m-d'],
        ]);

        $fecini = $data['fecini'];
        $fecfin = $data['fecfin'];
        $generalService = new GeneralService;

        $rows = Gener02::select(
            'gener02.usuario',
            'gener02.nombre',
            DB::raw('COUNT(*) as cantidad'),
            'mercurio08.tipopc',
            'mercurio09.detalle',
            'mercurio09.dias'
        )
            ->join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')
            ->join('mercurio09', 'mercurio08.tipopc', '=', 'mercurio09.tipopc')
            ->groupBy('mercurio08.tipopc', 'gener02.usuario', 'gener02.nombre', 'mercurio09.detalle', 'mercurio09.dias')
            ->get()
            ->map(function ($item) use ($generalService, $fecini, $fecfin) {
                $item = $item->toArray();
                $tipopc = (string) $item['tipopc'];
                $usuario = (string) $item['usuario'];
                $diasLimite = (int) ($item['dias'] ?? 0);

                $conteos = [];
                $totalVencido = 0;

                foreach (['A' => 'estado_aprobado', 'R' => 'estado_rechazo', 'P' => 'estado_pendiente', 'D' => 'estado_devuelto'] as $estado => $campo) {
                    $condi = "estado='{$estado}' and fecsol>='{$fecini}' and fecsol<='{$fecfin}'";
                    $result = $generalService->consultaTipopc($tipopc, 'count', '', $usuario, $condi);
                    $conteos[$campo] = (int) ($result['count'] ?? 0);

                    foreach ($result['all'] ?? [] as $mmercurio) {
                        $id = is_object($mmercurio)
                            ? (method_exists($mmercurio, 'getId') ? $mmercurio->getId() : ($mmercurio->id ?? null))
                            : ($mmercurio['id'] ?? null);
                        if ($id === null) {
                            continue;
                        }
                        $diasVencidos = CalculatorDias::calcular($tipopc, $id);
                        if ($diasVencidos > $diasLimite) {
                            $totalVencido++;
                        }
                    }
                }

                return [
                    'usuario' => $usuario,
                    'nombre' => (string) $item['nombre'],
                    'tipopc' => $tipopc,
                    'detalle' => (string) $item['detalle'],
                    'dias' => $diasLimite,
                    'estado_aprobado' => $conteos['estado_aprobado'],
                    'estado_rechazo' => $conteos['estado_rechazo'],
                    'estado_pendiente' => $conteos['estado_pendiente'],
                    'estado_devuelto' => $conteos['estado_devuelto'],
                    'total' => array_sum($conteos),
                    'total_vencido' => $totalVencido,
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'filters' => [
                'fecini' => $fecini,
                'fecfin' => $fecfin,
            ],
            'rows' => $rows,
            'count' => count($rows),
        ]);
    }

    public function certificadoTrabajadorView(): Response
    {
        $tipos = [
            'A' => 'Certificación afiliación principal',
            'I' => 'Certificación con núcleo',
            'T' => 'Certificación de multiafiliación',
            'P' => 'Reporte trabajador en planillas',
        ];

        return Inertia::render('Cajas/Consulta/CertificadoTrabajador', [
            'title' => 'Certificado para Trabajador',
            'tipos' => collect($tipos)
                ->map(fn (string $label, string $value) => ['value' => $value, 'label' => $label])
                ->values()
                ->all(),
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
