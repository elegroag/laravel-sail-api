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
            'title' => 'Consulta',
        ]);
    }

    public function consulta_auditoria_viewAction()
    {
        return view('cajas.consulta.consulta_auditoria_view', [
            'title' => 'Consulta Historica',
        ]);
    }

    public function consulta_auditoriaAction(Request $request)
    {
        $this->setResponse('ajax');
        $tipopc = $request->input('tipopc');
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');
        $html = view('cajas.consulta.tmp.consulta_auditoria', compact('tipopc', 'fecini', 'fecfin'))->render();
        return $this->renderObject(['consulta' => $html], false);
    }

    public function reporte_auditoriaAction(Request $request)
    {
        /* $this->setResponse('view');
        $tipopc = $request->input('tipopc');
        $fecini = $request->input('fecini');
        $fecfin = $request->input('fecfin');
        $fecha = new \DateTime;
        $file = 'public/temp/' . 'reporte_auditoria_' . $fecha->format('Ymd') . '.xls';

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
                $extra = $mmercurio->getCampoDetalle() . ' - ' . $mmercurio->getAntval() . ' - ' . $mmercurio->getValor();
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
        header('location: ' . env('APP_URL') . "/{$file}"); */
    }
}
