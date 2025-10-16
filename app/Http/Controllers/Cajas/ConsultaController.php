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

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function indexAction()
    {
        return view('cajas.consulta.index', [
            'title' => 'Consulta',
        ]);
    }

    public function cargaLaboralAction()
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

    public function reporteExcelCargaLaboralAction(ReportService $reportService)
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

    public function reporteExcelIndicadoresAction($fecini, $fecfin, ReportService $reportService)
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

    public function indicadoresAction()
    {
        return view('cajas.consulta.indicadores', [
            'title' => 'Consulta Indicadores'
        ]);
    }

    public function consultaIndicadoresAction(Request $request)
    {
        $this->setResponse('ajax');
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');
        $mercurio09 = (new Mercurio09)->find('conditions: tipopc IN(1,2,3,4,8,9,13)');

        $html = "<div class='table-responsive'>";
        $html .= "<table class='table table-bordered table-hover table-sm'>";
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Usuario</th>';
        $html .= "<th>Acc\Estado</th>";

        $estados = new Mercurio31;
        $estados = $estados->getEstadoArray();

        foreach ($estados as $mestados) {
            $html .= "<th class='text-center'>";
            $html .= "$mestados ";
            $html .= '</th>';
        }
        $html .= "<th class='text-center'>";
        $html .= 'TOT';
        $html .= '</th>';
        $html .= "<th class='text-center'>VEN</th>";
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $gener02 = (new Gener02)->findAllBySql('SELECT distinct gener02.usuario,
			gener02.nombre,
			gener02.login
		FROM gener02, mercurio08
		where gener02.usuario=mercurio08.usuario');

        foreach ($gener02 as $mgener02) {

            $first = true;
            foreach ($mercurio09 as $mmercurio09) {

                $html .= '<tr>';
                if ($first) {
                    $html .= "<th class='align-middle' rowspan='" . $mercurio09->count() . "'>{$mgener02->getUsuario()}-{$mgener02->getNombre()}</th>";
                }
                $first = false;
                $html .= "<td>{$mmercurio09->getDetalle()}</td>";

                $valores_estado = '';
                $total_estado = 0;
                foreach ($estados as $key => $mestados) {
                    $condi = "estado='$key' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin'";

                    $consultasOldServices = new GeneralService;
                    $result = $consultasOldServices->consultaTipopc($mmercurio09->getTipopc(), 'count', null, $mgener02->getUsuario(), $condi);
                    $html .= "<td align='center'>";
                    $html .= "{$result['count']}";
                    $html .= '</td>';
                    $total_estado += $result['count'];
                }
                $condi = "estado<>'T' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin'";

                $consultasOldServices = new GeneralService;
                $result = $consultasOldServices->consultaTipopc($mmercurio09->getTipopc(), 'count', null, $mgener02->getUsuario(), $condi);
                $mercurio = $result['all'];
                $total_vencido = 0;

                foreach ($mercurio as $mmercurio) {
                    $dias_vencidos = CalculatorDias::calcular(
                        $mmercurio09->getTipopc(),
                        $mmercurio->getId()
                    );

                    if ($dias_vencidos > $mmercurio09->getDias() && $mmercurio->getEstado() == 'P') {
                        $total_vencido++;
                    }
                }

                $html .= "<td align='center'>$total_estado</td>";
                $html .= "<td align='center'>$total_vencido</td>";
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        return $this->renderText($html);
    }

    public function consultaActivacionMasivaViewAction()
    {
        return view('cajas.consulta.activacion_masiva', [
            'title' => 'Consulta Activacion Masiva',
        ]);
    }

    public function consultaActivacionMasivaAction(Request $request)
    {
        $this->setResponse('ajax');
        // $nit = $request->input("nit");
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

    public function reasignaViewAction()
    {
        $gener02 = Gener02::where('estado', 'A')->join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')->get();
        $data_usuarios = $gener02->pluck('nombre', 'usuario');
        $data_mercurio09 = Mercurio09::all()->pluck('detalle', 'tipopc');
        $accion = array('C' => 'CONSULTA', 'P' => 'PROCESO');

        return view('cajas.consulta.reasigna', [
            'title' => 'Consulta Reasigna',
            'data_usuarios' => $data_usuarios->toArray(),
            'data_mercurio09' => $data_mercurio09->toArray(),
            'accion' => $accion
        ]);
    }
}
