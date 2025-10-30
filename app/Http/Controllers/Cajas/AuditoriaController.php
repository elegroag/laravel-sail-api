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
        $condi = " mercurio10.fecsis>='$fecini' and mercurio10.fecsis<='$fecfin' ";
        $mercurio = $this->consultaTipopc($tipopc, 'all', '', '', $condi);

        $html = view('cajas.auditoria._consulta', [
            'tipopc' => $tipopc,
            'fecini' => $fecini,
            'fecfin' => $fecfin,
            'mercurio' => $mercurio,
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

    public function consultaTipopc($tipopc, $tipo_consulta, $numero = "", $usuario = "", $condi = "")
    {
        $condi_extra = "";
        if ($condi != "") $condi_extra = " $condi";
        $params = [
            'tipopc' => $tipopc,
            'tipo_consulta' => $tipo_consulta,
            'numero' => $numero,
            'usuario' => $usuario,
            'condi_extra' => $condi_extra,
        ];

        if ($tipopc == "1") {
            $entityService = new TrabajadorService();
        }
        if ($tipopc == "2") {
            $entityService = new EmpresaService();
        }
        if ($tipopc == "3") {
            $entityService = new ConyugeService();
        }
        if ($tipopc == "4") {
            $entityService = new BeneficiarioService();
        }
        if ($tipopc == "5") {
            $entityService = new ActualizaEmpresaService();
        }
        if ($tipopc == "6") {
            $entityService = new DatosTrabajadorService();
        }
        if ($tipopc == "7") {
            $entityService = new RetiroService();
        }
        if ($tipopc == "8") {
            $entityService = new CertificadoService();
        }
        if ($tipopc == "10") {
            $entityService = new FacultativoService();
        }
        if ($tipopc == "9") {
            $entityService = new PensionadoService();
        }
        if ($tipopc == "11") {
            $entityService = new EmpresaService();
            //Mercurio39
        }
        if ($tipopc == "12") {
            //Mercurio40
            $entityService = new EmpresaService();
        }

        $out = $entityService->consultaTipopc(
            new Srequest($params)
        );

        $response = [
            'datos' => ($out['datos'] ?? []),
            'consulta' => ($out['consulta'] ?? ''),
            'campos' => ($out['campos'] ?? []),
            'count' => ($out['count'] ?? 0),
            'all' => ($out['all'] ?? []),
        ];
        return $response;
    }
}
