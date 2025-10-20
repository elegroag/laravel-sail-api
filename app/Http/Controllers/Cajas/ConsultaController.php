<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Gener02;
use App\Models\Mercurio09;
use App\Models\Mercurio20;
use App\Models\Mercurio31;
use App\Models\Mercurio46;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\GeneralService;
use App\Services\ReportGenerator\ReportService;
use Generator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultaController extends ApplicationController
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
        return view('cajas.consulta.index', [
            'title' => 'Consulta',
        ]);
    }

    public function cargaLaboral()
    {
        $gener02 = Gener02::select('gener02.usuario', 'gener02.nombre', 'gener02.login')
            ->join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')
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
                $out =  $generalService->consultaTipopc($item['tipopc'], 'count', '', $item['usuario']);
                $item['cantidad'] = $out['count'];
                return $item;
            });

        return view('cajas.consulta.carga_laboral', [
            'title' => 'Carga Laboral',
            'gener02' => $gener02,
            'mercurio09' => $mercurio09
        ]);
    }

    public function reporteExcelCargaLaboral(ReportService $reportService)
    {
        $fecha = new \DateTime;
        $filename = 'reporte_carga_laboral' . $fecha->format('Ymd') . '.xlsx';

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
                    $condi = ["estado" => 'P'];
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
        $filename = 'reporte_indicadores' . $fecha->format('Ymd') . '.xlsx';

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
                    $headers[] = $mmercurio09->getDetalle() . ' - ' . $label;
                }
                $headers[] = $mmercurio09->getDetalle() . ' - TOT';
                $headers[] = $mmercurio09->getDetalle() . ' - VEN';
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
            'title' => 'Consulta Indicadores'
        ]);
    }

    public function consultaIndicadores(Request $request)
    {
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');
        $generalService = new GeneralService();
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
            "success" => true
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
            $html .= "<a href='#' onclick='descarga_activacion(this)'>" . $mmercurio->getArchivo() . '</a>';
            $html .= '</td>';
            $html .= '</tr>';
        }

        return $this->renderObject(['consulta' => $html], false);
    }
}
