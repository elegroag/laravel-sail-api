<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Gener02;
use App\Models\Mercurio09;
use App\Models\Mercurio20;
use App\Models\Mercurio31;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\GeneralService;
use Illuminate\Http\Request;

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
        $help = 'Esta opcion permite manejar los ';
        $this->setParamToView('help', $help);
        $this->setParamToView('title', 'Consulta');
        // Tag::setDocumentTitle('Consulta');
    }

    public function consulta_auditoria_viewAction()
    {
        $help = 'Esta opcion permite manejar los ';
        $this->setParamToView('help', $help);
        $this->setParamToView('title', 'Consulta Historica');
        // Tag::setDocumentTitle('Consulta Historica');
    }

    public function consulta_auditoriaAction(Request $request)
    {
        $this->setResponse('ajax');
        $tipopc = $request->input('tipopc');
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');
        $html = '';
        $html = "<div class='table-responsive'> ";
        $html .= "<table class='table'>";
        $html .= '<tr>';
        $html .= '<td>Documento</td>';
        $html .= '<td>Nombre</td>';
        $html .= '<td>Responsable</td>';
        $html .= '<td>Fecha</td>';
        $html .= '<td>Dias</td>';
        if ($tipopc == '8' || $tipopc == '5') {
            $html .= "<th scope='col'></th>";
        }
        $html .= '<td>Estado</td>';
        $html .= '</tr>';
        $condi = " estado<>'T' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin' ";
        $condi = " mercurio10.fecsis>='$fecini' and mercurio10.fecsis<='$fecfin' ";

        $consultasOldServices = new GeneralService;
        $mercurio = $consultasOldServices->consultaTipopc($tipopc, 'all', '', '', $condi);

        foreach ($mercurio['datos'] as $mmercurio) {
            if ($tipopc == 1 || $tipopc == 9 || $tipopc == 10 || $tipopc == 11 || $tipopc == 12) { // trabajador
                $documento = 'getCedtra';
                $nombre = 'getNombre';
            }
            if ($tipopc == 2) { // empresa
                $documento = 'getNit';
                $nombre = 'getRazsoc';
            }
            if ($tipopc == 3) { // conyuge
                $documento = 'getCedcon';
                $nombre = 'getNombre';
            }
            if ($tipopc == 4) { // beneficiario
                $documento = 'getNumdoc';
                $nombre = 'getNombre';
            }
            if ($tipopc == 5) { // basicos
                $documento = 'getDocumento';
                $nombre = 'getDocumentoDetalle';
                $extra = $mmercurio->getCampoDetalle().' - '.$mmercurio->getAntval().' - '.$mmercurio->getValor();
            }
            if ($tipopc == 7) { // retiro
                $documento = 'getCedtra';
                $nombre = 'getNomtra';
            }
            if ($tipopc == 8) { // certificiados
                $documento = 'getCodben';
                $nombre = 'getNombre';
                $extra = $mmercurio->getNomcer();
            }
            $gener02 = $this->Gener02->findFirst("usuario = '{$mmercurio->getUsuario()}'");
            if ($gener02 == false) {
                $gener02 = new Gener02;
            }
            $mercurio20 = $this->Mercurio20->findFirst("log = '{$mmercurio->getLog()}'");
            if ($mercurio20 == false) {
                $mercurio20 = new Mercurio20;
            }

            // Debug::addVariable("d",print_r($mmercurio,true));
            // throw new DebugException();
            $dias_vencidos = CalculatorDias::calcular(
                $tipopc,
                $mmercurio->getId()
            );

            $html .= '<tr>';
            $html .= '<tr>';
            $html .= "<td>{$mmercurio->$documento()}</td>";
            $html .= "<td>{$mmercurio->$nombre()}</td>";
            $html .= "<td>{$gener02->getNombre()}</td>";
            // $html .= "<td>{$mercurio20->getFecha()}</td>";
            $html .= "<td>{$mmercurio->getFecest()->getUsingFormatDefault()}</td>";
            $html .= "<td>{$dias_vencidos}</td>";
            if ($tipopc == '8' || $tipopc == '5') {
                $html .= "<td>$extra</td>";
            }
            $html .= "<td>{$mmercurio->getEstadoDetalle()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Info' onclick=\"info('$tipopc','{$mmercurio->getId()}')\">";
            $html .= "<i class='fas fa-info'></i>";
            $html .= '</a>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</tr>';
        }

        return $this->renderText($html, false);
    }

    public function reporte_auditoriaAction(Request $request)
    {
        $this->setResponse('view');
        $tipopc = $request->input('tipopc');
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');
        $fecha = new \DateTime;
        $file = 'public/temp/'.'reporte_auditoria_'.$fecha->format('Ymd').'.xls';

        $excels = new Spreadsheet_Excel_Writer($file);
        $excel = $excels->addWorksheet();
        $column_title = $excels->addFormat([
            'fontfamily' => 'Verdana',
            'size' => 12,
            'fgcolor' => 12,
            'border' => 1,
            'bordercolor' => 'black',
            'halign' => 'center',
        ]);
        $title = $excels->addFormat([
            'fontfamily' => 'Verdana',
            'size' => 13,
            'border' => 0,
            'bordercolor' => 'black',
            'halign' => 'center',
        ]);
        $column_style = $excels->addFormat([
            'fontfamily' => 'Verdana',
            'size' => 11,
            'border' => 1,
            'bordercolor' => 'black',
        ]);
        $excel->setMerge(0, 1, 0, 6);
        $excel->write(0, 1, 'Reporte De Consulta Laboral', $title);
        $excel->setColumn(1, 20, 45);
        $i = 0;
        $j = 2;
        $excel->write($j, $i++, 'Documento', $column_title);
        $excel->write($j, $i++, 'Nombre', $column_title);
        $excel->write($j, $i++, 'Responsable', $column_title);
        $excel->write($j, $i++, 'Fecha', $column_title);
        $excel->write($j, $i++, 'Dias', $column_title);
        if ($tipopc == '8' || $tipopc == '5') {
            $excel->write($j, $i++, 'Extra', $column_title);
        }
        $excel->write($j, $i++, 'Estado', $column_title);
        $j++;
        $condi = " estado<>'T' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin' ";
        $condi = " mercurio10.fecsis>='$fecini' and mercurio10.fecsis<='$fecfin' ";

        $consultasOldServices = new GeneralService;
        $mercurio = $consultasOldServices->consultaTipopc($tipopc, 'all', '', '', $condi);
        foreach ($mercurio['datos'] as $mmercurio) {
            $i = 0;
            if ($tipopc == 1 || $tipopc == 9 || $tipopc == 10 || $tipopc == 11 || $tipopc == 12) { // trabajador
                $documento = 'getCedtra';
                $nombre = 'getNombre';
            }
            if ($tipopc == 2) { // empresa
                $documento = 'getNit';
                $nombre = 'getRazsoc';
            }
            if ($tipopc == 3) { // conyuge
                $documento = 'getCedcon';
                $nombre = 'getNombre';
            }
            if ($tipopc == 4) { // beneficiario
                $documento = 'getDocumento';
                $nombre = 'getNombre';
            }
            if ($tipopc == 5) { // basicos
                $documento = 'getDocumento';
                $nombre = 'getDocumentoDetalle';
                $extra = $mmercurio->getCampoDetalle().' - '.$mmercurio->getAntval().' - '.$mmercurio->getValor();
            }
            if ($tipopc == 7) { // retiro
                $documento = 'getCedtra';
                $nombre = 'getNomtra';
            }
            if ($tipopc == 8) { // certificiados
                $documento = 'getCodben';
                $nombre = 'getNombre';
                $extra = $mmercurio->getNomcer();
            }
            $gener02 = $this->Gener02->findFirst("usuario = '{$mmercurio->getUsuario()}'");
            if ($gener02 == false) {
                $gener02 = new Gener02;
            }
            $mercurio20 = $this->Mercurio20->findFirst("log = '{$mmercurio->getLog()}'");
            if ($mercurio20 == false) {
                $mercurio20 = new Mercurio20;
            }

            $dias_vencidos = CalculatorDias::calcular(
                $tipopc,
                $mmercurio->getId()
            );

            $excel->write($j, $i++, $mmercurio->$documento(), $column_style);
            $excel->write($j, $i++, $mmercurio->$nombre(), $column_style);
            $excel->write($j, $i++, $gener02->getNombre(), $column_style);
            // $excel->write($j, $i++, $mercurio20->getFecha(), $column_style);
            $excel->write($j, $i++, $mmercurio->getFecest()->getUsingFormatDefault(), $column_style);
            $excel->write($j, $i++, $dias_vencidos, $column_style);
            if ($tipopc == '8' || $tipopc == '5') {
                $excel->write($j, $i++, $extra, $column_style);
            }
            $excel->write($j, $i++, $mmercurio->getEstadoDetalle(), $column_style);
            $j++;
        }
        $excels->close();
        header('location: '.env('APP_URL')."/{$file}");
    }

    public function inforAction(Request $request, $id)
    {
        $this->setResponse('ajax');
        $tipopc = $request->input('tipopc');
        $id = $request->input('id');
        $response = '';
        $consultasOldServices = new GeneralService;
        $result = $consultasOldServices->consultaTipopc($tipopc, 'info', $id);
        $response = $result['consulta'];

        return $this->renderText($response);
    }

    public function carga_laboralAction()
    {
        $help = 'Esta opcion permite manejar los ';
        $this->setParamToView('help', $help);
        $this->setParamToView('title', 'Carga Laboral');
        // Tag::setDocumentTitle('Carga Laboral');
        $this->setParamToView('buttons', ['P' => ['btyp' => 'btn-neutral', 'func' => 'reporte_excel_carga_laboral()', 'glyp' => 'fas fa-file-contract', 'valr' => 'Reporte']]);
        $gener02 = $this->Gener02->findAllBySql('select distinct gener02.usuario,gener02.nombre,gener02.login from gener02,mercurio08 where gener02.usuario=mercurio08.usuario');
        $mercurio09 = $this->Mercurio09->find();

        $html = "<div class='card-body'>";
        $html .= "<div class='card-columns'>";

        foreach ($gener02 as $mgener02) {
            $html .= "<div class='card'>";
            $html .= "<div class='card-header bg-transparent text-center'>";
            $html .= "<h5 class='h4 ls-1 py-0 mb-0'>{$mgener02->getNombre()}</h5>";
            $html .= '</div>';
            $html .= "<ul class='list-group list-group-flush'>";
            foreach ($mercurio09 as $mmercurio09) {
                if ($mmercurio09->getTipopc() == '2') {
                    $condi = " estado IN('P','D') ";
                } else {
                    $condi = " estado='P' ";
                }

                $consultasOldServices = new GeneralService;
                $result = $consultasOldServices->consultaTipopc($mmercurio09->getTipopc(), 'count', '', $mgener02->getUsuario(), $condi);
                $count = $result['count'];

                $html .= "<li class='list-group-item d-flex justify-content-between align-items-center py-2'>";
                $html .= '<small>'.ucwords(strtolower($mmercurio09->getDetalle())).'</small>';
                $html .= "<span class='badge badge-md badge-primary badge-pill'>$count</span>";
                $html .= '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</div>';

        $this->setParamToView('html', $html);
    }

    public function bkp_carga_laboralAction()
    {
        $help = 'Esta opcion permite manejar los ';
        $this->setParamToView('help', $help);
        $this->setParamToView('title', 'Carga Laboral');
        // Tag::setDocumentTitle('Carga Laboral');
        $this->setParamToView('buttons', ['P' => ['btyp' => 'btn-neutral', 'func' => 'reporte_excel_carga_laboral()', 'glyp' => 'fas fa-file-contract', 'valr' => 'Reporte']]);

        $html = "<div class='table-responsive'> ";
        $html .= "<table class='table'>";
        $html .= '<tr>';
        $html .= '<td>Usuario/Movimiento</td>';
        $mercurio09 = $this->Mercurio09->find();
        foreach ($mercurio09 as $mmercurio09) {
            $html .= "<td>{$mmercurio09->getDetalle()}</td>";
        }
        $html .= '</tr>';
        $gener02 = $this->Gener02->findAllBySql('select distinct gener02.usuario,gener02.nombre,gener02.login from gener02,mercurio08 where gener02.usuario=mercurio08.usuario');
        foreach ($gener02 as $mgener02) {
            $html .= '<tr>';
            $html .= "<td>{$mgener02->getNombre()}</td>";
            foreach ($mercurio09 as $mmercurio09) {
                $condi = "estado='P'";

                $consultasOldServices = new GeneralService;
                $result = $consultasOldServices->consultaTipopc($mmercurio09->getTipopc(), 'count', '', $mgener02->getUsuario(), $condi);

                $count = $result['count'];
                $html .= "<td>{$count}</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        $html .= '</div>';
        $this->setParamToView('html', $html);
    }

    public function reporte_excel_carga_laboralAction()
    {
        $this->setResponse('view');
        $fecha = new \DateTime;
        $file = 'public/temp/'.'reporte_carga_laboral'.$fecha->format('Ymd').'.xls';

        $excels = new Spreadsheet_Excel_Writer($file);
        $excel = $excels->addWorksheet();
        $column_title = $excels->addFormat([
            'fontfamily' => 'Verdana',
            'size' => 12,
            'fgcolor' => 12,
            'border' => 1,
            'bordercolor' => 'black',
            'halign' => 'center',
        ]);
        $title = $excels->addFormat([
            'fontfamily' => 'Verdana',
            'size' => 13,
            'border' => 0,
            'bordercolor' => 'black',
            'halign' => 'center',
        ]);
        $column_style = $excels->addFormat([
            'fontfamily' => 'Verdana',
            'size' => 11,
            'border' => 1,
            'bordercolor' => 'black',
        ]);
        $excel->setMerge(0, 1, 0, 6);
        $excel->write(0, 1, 'Reporte De Carga Laboral', $title);
        $excel->setColumn(1, 20, 45);
        $i = 0;
        $j = 2;
        $excel->write($j, $i++, 'Usuario/Movimiento', $column_title);
        $mercurio09 = $this->Mercurio09->find();
        foreach ($mercurio09 as $mmercurio09) {
            $excel->write($j, $i++, $mmercurio09->getDetalle(), $column_title);
        }
        $j++;
        $gener02 = $this->Gener02->findAllBySql('select distinct gener02.usuario,gener02.nombre,gener02.login from gener02,mercurio08 where gener02.usuario=mercurio08.usuario');
        foreach ($gener02 as $mgener02) {
            $i = 0;
            $excel->write($j, $i++, $mgener02->getNombre(), $column_style);
            foreach ($mercurio09 as $mmercurio09) {
                $condi = "estado='P'";

                $consultasOldServices = new GeneralService;
                $result = $consultasOldServices->consultaTipopc($mmercurio09->getTipopc(), 'count', '', $mgener02->getUsuario(), $condi);

                $count = $result['count'];
                $excel->write($j, $i++, $count, $column_style);
            }
            $j++;
        }
        $excels->close();
        header('location: '.env('APP_URL')."/{$file}");
    }

    public function reporte_excel_indicadoresAction($fecini, $fecfin)
    {
        $this->setResponse('view');
        $fecha = new \DateTime;
        $file = 'public/temp/'.'reporte_indicadores'.$fecha->format('Ymd').'.xls';

        $excels = new Spreadsheet_Excel_Writer($file);
        $excel = $excels->addWorksheet();
        $column_title = $excels->addFormat([
            'fontfamily' => 'Verdana',
            'size' => 12,
            'fgcolor' => 12,
            'border' => 1,
            'bordercolor' => 'black',
            'halign' => 'center',
        ]);
        $title = $excels->addFormat([
            'fontfamily' => 'Verdana',
            'size' => 13,
            'border' => 0,
            'bordercolor' => 'black',
            'halign' => 'center',
        ]);
        $column_style = $excels->addFormat([
            'fontfamily' => 'Verdana',
            'size' => 11,
            'border' => 1,
            'bordercolor' => 'black',
        ]);
        $excel->setMerge(0, 1, 0, 6);
        $excel->write(0, 1, 'Reporte De Indicadores', $title);
        $excel->setColumn(0, 0, 45);
        $estados = new Mercurio31;
        $estados = $estados->getEstadoArray();
        $i = 0;
        $j = 2;
        $excel->write($j, $i++, 'Usuario/Movimiento', $column_title);
        $excel->setMerge(2, 1, 2, 6);
        $excel->setMerge(2, 7, 2, 12);
        $excel->setMerge(2, 13, 2, 18);
        $excel->setMerge(2, 19, 2, 24);
        $excel->setMerge(2, 25, 2, 30);
        $excel->setMerge(2, 31, 2, 36);
        $excel->setMerge(2, 37, 2, 42);
        $excel->setMerge(2, 43, 2, 48);
        $excel->setMerge(2, 49, 2, 54);
        $excel->setMerge(2, 55, 2, 60);
        $excel->setMerge(2, 61, 2, 66);
        $excel->setMerge(2, 67, 2, 72);
        $mercurio09 = $this->Mercurio09->find();
        $i = 1;
        foreach ($mercurio09 as $mmercurio09) {
            $excel->write($j, $i, $mmercurio09->getDetalle(), $column_title);
            $i += 6;
        }
        $i = 0;
        $j++;
        $excel->write($j, $i++, '', $column_style);
        foreach ($mercurio09 as $mmercurio09) {
            foreach ($estados as $mestados) {
                $excel->write($j, $i++, $mestados, $column_style);
            }
            $excel->write($j, $i++, 'TOT', $column_style);
            $excel->write($j, $i++, 'VEN', $column_style);
        }
        $j++;
        $gener02 = $this->Gener02->findAllBySql('select distinct gener02.usuario,gener02.nombre,gener02.login from gener02,mercurio08 where gener02.usuario=mercurio08.usuario');
        foreach ($gener02 as $mgener02) {
            $i = 0;
            $excel->write($j, $i++, $mgener02->getNombre(), $column_style);
            foreach ($mercurio09 as $mmercurio09) {
                $valores_estado = '';
                $total_estado = 0;
                foreach ($estados as $key => $mestados) {
                    $condi = "estado='$key' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin'";

                    $consultasOldServices = new GeneralService;
                    $result = $consultasOldServices->consultaTipopc($mmercurio09->getTipopc(), 'count', '', $mgener02->getUsuario(), $condi);
                    $count = $result['count'];
                    $excel->write($j, $i++, $count, $column_style);
                    $total_estado += $result['count'];
                }
                $condi = "estado<>'T' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin'";

                $consultasOldServices = new GeneralService;
                $result = $consultasOldServices->consultaTipopc($mmercurio09->getTipopc(), 'count', '', $mgener02->getUsuario(), $condi);

                $mercurio = $result['all'];
                $total_vencido = 0;
                foreach ($mercurio as $mmercurio) {

                    $dias_vencidos = CalculatorDias::calcular(
                        $mmercurio09->getTipopc(),
                        $mmercurio09->getId()
                    );

                    if ($dias_vencidos > $mmercurio09->getDias()) {
                        $total_vencido++;
                    }
                }
                $excel->write($j, $i++, $total_estado, $column_style);
                $excel->write($j, $i++, $total_vencido, $column_style);
            }
            $j++;
        }
        $excels->close();
        header('location: '.env('APP_URL')."/{$file}");
    }

    public function indicadoresAction()
    {
        $help = 'Esta opcion permite manejar los ';
        $this->setParamToView('help', $help);
        $this->setParamToView('title', 'Indicadores');
        // Tag::setDocumentTitle('Carga Laboral');
        $this->setParamToView('buttons', ['P' => ['btyp' => 'btn-neutral', 'func' => 'reporte_excel_indicadores()', 'glyp' => 'fas fa-file-contract', 'valr' => 'Reporte']]);
    }

    public function consulta_indicadoresAction(Request $request)
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
                    $html .= "<th class='align-middle' rowspan='".$mercurio09->count()."'>{$mgener02->getUsuario()}-{$mgener02->getNombre()}</th>";
                }
                $first = false;
                $html .= "<td>{$mmercurio09->getDetalle()}</td>";

                $valores_estado = '';
                $total_estado = 0;
                foreach ($estados as $key => $mestados) {
                    $condi = "estado='$key' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin'";

                    $consultasOldServices = new GeneralService;
                    $result = $consultasOldServices->consultaTipopc($mmercurio09->getTipopc(), 'count', '', $mgener02->getUsuario(), $condi);
                    $html .= "<td align='center'>";
                    $html .= "{$result['count']}";
                    $html .= '</td>';
                    $total_estado += $result['count'];
                }
                $condi = "estado<>'T' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin'";

                $consultasOldServices = new GeneralService;
                $result = $consultasOldServices->consultaTipopc($mmercurio09->getTipopc(), 'count', '', $mgener02->getUsuario(), $condi);
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

    public function consulta_activacion_masiva_viewAction()
    {
        $help = 'Esta opcion permite manejar los ';
        $this->setParamToView('help', $help);
        $this->setParamToView('title', 'Consulta Activacion Masiva');
        // Tag::setDocumentTitle('Consulta Activacion Masiva');
    }

    public function consulta_activacion_masivaAction(Request $request)
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
        $mercurio = $this->Mercurio46->find($condi);
        foreach ($mercurio as $mmercurio) {
            $html .= '<tr>';
            $html .= '<tr>';
            $html .= "<td>{$mmercurio->getId()}</td>";
            $html .= "<td>{$mmercurio->getNit()}</td>";
            $html .= "<td>{$mmercurio->getFecsis()}</td>";
            $html .= '<td>';
            $html .= "<a href='#' onclick='descarga_activacion(this)'>".$mmercurio->getArchivo().'</a>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</tr>';
        }

        return $this->renderText($html);
    }
}
