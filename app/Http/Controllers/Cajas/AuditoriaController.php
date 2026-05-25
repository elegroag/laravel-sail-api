<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Gener02;
use App\Models\Mercurio09;
use App\Models\Mercurio20;
use App\Models\Mercurio31;
use App\Services\Entidades\ActualizaEmpresaService;
use App\Services\Entidades\BeneficiarioService;
use App\Services\Entidades\CertificadoService;
use App\Services\Entidades\ConyugeService;
use App\Services\Entidades\DatosTrabajadorService;
use App\Services\Entidades\EmpresaService;
use App\Services\Entidades\FacultativoService;
use App\Services\Entidades\PensionadoService;
use App\Services\Entidades\RetiroService;
use App\Services\Entidades\TrabajadorService;
use App\Services\ReportGenerator\Factories\OptimizedReportFactory;
use App\Services\ReportGenerator\ReportService;
use App\Services\Srequest;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\GeneralService;
use Illuminate\Http\Request;

class AuditoriaController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipfun;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user');
        $this->tipfun = session('tipfun');
    }

    public function index()
    {
        return view('cajas.auditoria.index', [
            'title' => 'Consulta Historica',
            'mercurio09' => Mercurio09::all(),
        ]);
    }

    public function consultaAuditoria(Request $request)
    {
        $this->setResponse('view');
        $tipopc = $request->input('tipopc');
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');
        $page = (int) $request->input('page', 1);

        // Validar fechas requeridas
        if (empty($fecini) || empty($fecfin)) {
            $html = '<div class="alert alert-warning">Debe ingresar fecha inicial y final.</div>';
            return $this->renderText($html);
        }

        $condi = " mercurio10.fecsis>='$fecini' and mercurio10.fecsis<='$fecfin' ";
        $mercurio = $this->consultaTipopc($tipopc, 'all', '', '', $condi, $page);

        // Pre-cargar gener02 y mercurio20 para la vista (evita $this-> en Blade)
        $usuarios = collect($mercurio['datos'] ?? [])->pluck('usuario')->unique();
        $logs = collect($mercurio['datos'] ?? [])->pluck('log')->unique();
        $gener02Map = Gener02::whereIn('usuario', $usuarios)->get()->keyBy('usuario');
        $mercurio20Map = Mercurio20::whereIn('log', $logs)->get()->keyBy('log');

        $html = view('cajas.auditoria._consulta', [
            'tipopc' => $tipopc,
            'fecini' => $fecini,
            'fecfin' => $fecfin,
            'mercurio' => $mercurio,
            'gener02Map' => $gener02Map,
            'mercurio20Map' => $mercurio20Map,
            'paginate' => $mercurio['paginate'] ?? null,
        ])->render();

        return $this->renderText($html);
    }

    public function reporteAuditoria(Request $request)
    {
        $format = strtolower($request->input('format', 'csv'));
        $tipopc = $request->input('tipopc');
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');

        $condi = " mercurio10.fecsis>='$fecini' and mercurio10.fecsis<='$fecfin' ";
        $consultasOldServices = new GeneralService;
        $mercurio = $consultasOldServices->consultaTipopc($tipopc, 'all', '', '', $condi);

        $hasExtra = ($tipopc == '8' || $tipopc == '5');
        $headers = ['Documento', 'Nombre', 'Responsable', 'Fecha', 'Dias'];
        if ($hasExtra) {
            $headers[] = 'Extra';
        }
        $headers[] = 'Estado';

        $generator = (function () use ($mercurio, $tipopc, $hasExtra, $headers) {
            yield $headers;
            foreach ($mercurio['datos'] as $mmercurio) {
                if ($tipopc == 1 || $tipopc == 9 || $tipopc == 10 || $tipopc == 11 || $tipopc == 12) {
                    $documento = 'getCedtra';
                    $nombre = 'getNombre';
                } elseif ($tipopc == 2) {
                    $documento = 'getNit';
                    $nombre = 'getRazsoc';
                } elseif ($tipopc == 3) {
                    $documento = 'getCedcon';
                    $nombre = 'getNombre';
                } elseif ($tipopc == 4) {
                    $documento = 'getDocumento';
                    $nombre = 'getNombre';
                } elseif ($tipopc == 5) {
                    $documento = 'getDocumento';
                    $nombre = 'getDocumentoDetalle';
                } elseif ($tipopc == 7) {
                    $documento = 'getCedtra';
                    $nombre = 'getNomtra';
                } elseif ($tipopc == 8) {
                    $documento = 'getCodben';
                    $nombre = 'getNombre';
                } else {
                    $documento = 'getDocumento';
                    $nombre = 'getNombre';
                }

                $gener02 = Gener02::where('usuario', $mmercurio->getUsuario())->first();
                $responsable = $gener02 ? $gener02->getNombre() : '';

                $dias_vencidos = CalculatorDias::calcular($tipopc, $mmercurio->getId());

                $fila = [
                    $mmercurio->$documento(),
                    $mmercurio->$nombre(),
                    $responsable,
                    $mmercurio->getFecest(),
                    $dias_vencidos,
                ];

                if ($hasExtra) {
                    if ($tipopc == '5') {
                        $extra = $mmercurio->getCampoDetalle() . ' - ' . $mmercurio->getAntval() . ' - ' . $mmercurio->getValor();
                    } else {
                        $extra = $mmercurio->getNomcer();
                    }
                    $fila[] = $extra;
                }

                $fila[] = $mmercurio->getEstadoDetalle();
                yield $fila;
            }
        })();

        $factory = new OptimizedReportFactory();
        $service = new ReportService($factory);
        $fecha = new \DateTime();
        $ext = $format;
        $filename = 'reporte_auditoria_' . $fecha->format('Ymd') . '.' . $ext;
        return $service->generateAndStream($format, $generator, $filename);
    }

    public function info(Request $request, $id)
    {
        $this->setResponse('ajax');
        $tipopc = $request->input('tipopc');
        $id = $request->input('id');
        $response = $this->consultaTipopc($tipopc, 'info', $id);
        return $this->renderObject($response);
    }

    public function consultaTipopc($tipopc, $tipo_consulta, $numero = "", $usuario = "", $condi = "", $page = 1)
    {
        $condi_extra = "";
        if ($condi != "") $condi_extra = " $condi";
        $params = [
            'tipopc' => $tipopc,
            'tipo_consulta' => $tipo_consulta,
            'numero' => $numero,
            'usuario' => $usuario,
            'condi_extra' => $condi_extra,
            'page' => $page,
        ];

        $entityService = match (true) {
            $tipopc == "1" => new TrabajadorService(),
            $tipopc == "2" => new EmpresaService(),
            $tipopc == "3" => new ConyugeService(),
            $tipopc == "4" => new BeneficiarioService(),
            $tipopc == "5" => new ActualizaEmpresaService(),
            $tipopc == "6" => new DatosTrabajadorService(),
            $tipopc == "7" => new RetiroService(),
            $tipopc == "8" => new CertificadoService(),
            $tipopc == "9" => new PensionadoService(),
            $tipopc == "10" => new FacultativoService(),
            $tipopc == "11" => new EmpresaService(),
            $tipopc == "12" => new EmpresaService(),
            default => null,
        };

        if ($entityService === null) {
            return [
                'datos' => [],
                'consulta' => '',
                'campos' => [],
                'count' => 0,
                'paginate' => null,
                'all' => [],
            ];
        }

        $out = $entityService->consultaTipopc(
            new Srequest($params)
        );

        $response = [
            'datos' => ($out['datos'] ?? []),
            'consulta' => ($out['consulta'] ?? ''),
            'campos' => ($out['campos'] ?? []),
            'count' => ($out['count'] ?? 0),
            'paginate' => ($out['paginate'] ?? null),
            'all' => ($out['all'] ?? []),
        ];
        return $response;
    }
}
