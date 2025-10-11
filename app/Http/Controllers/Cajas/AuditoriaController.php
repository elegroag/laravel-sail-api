<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Gener02;
use App\Models\Mercurio09;
use App\Models\Mercurio20;
use App\Models\Mercurio31;
use App\Services\ReportGenerator\Factories\OptimizedReportFactory;
use App\Services\ReportGenerator\ReportService;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\GeneralService;
use Illuminate\Http\Request;

class AuditoriaController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function indexAction()
    {
        return view('cajas.auditoria.index', [
            'title' => 'Consulta Historica',
        ]);
    }

    public function consultaAuditoriaAction(Request $request)
    {
        $this->setResponse('ajax');
        $tipopc = $request->input('tipopc');
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');
        $html = view('cajas.consulta.tmp.consulta_auditoria', compact('tipopc', 'fecini', 'fecfin'))->render();
        return $this->renderObject(['consulta' => $html], false);
    }

    public function reporteAuditoriaAction(Request $request)
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
                    $mmercurio->getFecest()->getUsingFormatDefault(),
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
}
