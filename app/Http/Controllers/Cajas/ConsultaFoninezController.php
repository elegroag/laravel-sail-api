<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Gener02;
use App\Models\Mercurio20;
use App\Models\Mercurio31;
use App\Services\Utils\GeneralService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ConsultafoninezController extends ApplicationController
{
    protected $db;
    protected $user;
    protected $tipo;
    /**
     * $generalService variable
     * @var GeneralService
     */
    private $generalService;

    public function __construct()
    {
        $this->generalService = new GeneralService();
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function indexAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Consulta Beneficiarios");
        #Tag::setDocumentTitle('Consulta Beneficiarios');
        $datos_captura = $this->generalService->webService("datosconsultafoninez", array("sql" => "select mercurio81.codinf, xml4d088.nomcom, gener08.detciu from mercurio81,xml4d088,gener08 where mercurio81.codinf=xml4d088.codinf and xml4d088.divpol=gener08.codciu;"));

        $datos_captura = $datos_captura['data'];

        //throw new DebugException(0);
        $_ciudades = array();
        if (!empty($datos_captura)) {
            foreach ($datos_captura['result'] as $data) $_ciudades[$data['codinf']] = $data['detciu'] . " - " . $data["nomcom"];
        }
        $this->setParamToView("ciudades", $_ciudades);
    }

    public function consulta_auditoria_viewAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Consulta Historica");
        #Tag::setDocumentTitle('Consulta Historica');
    }

    public function consulta_auditoriaAction(Request $request)
    {
        $this->setResponse("ajax");
        $tipopc = $request->input("tipopc");
        $fecini = $request->input("fecini");
        $fecfin = $request->input("fecfin");
        $html = "";
        $html = "<div class='table-responsive'> ";
        $html .= "<table class='table'>";
        $html .= "<tr>";
        $html .= "<td>Documento</td>";
        $html .= "<td>Nombre</td>";
        $html .= "<td>Responsable</td>";
        $html .= "<td>Fecha</td>";
        $html .= "<td>Dias</td>";
        if ($tipopc == "8" || $tipopc == "5") $html .= "<th scope='col'></th>";
        $html .= "<td>Estado</td>";
        $html .= "</tr>";
        $condi = " estado<>'T' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin' ";
        $mercurio = $this->generalService->consultaTipopc($tipopc, "all", "", "", $condi);

        // throw new DebugException("Error Processing Request");

        foreach ($mercurio['datos'] as $mmercurio) {
            if ($tipopc == 1  || $tipopc == 11 || $tipopc == 12) { //trabajador
                $documento = "getCedtra";
                $nombre = "getNombre";
            }
            if ($tipopc == 2 || $tipopc == 14 || $tipopc == 9 || $tipopc == 13 || $tipopc == 10) { //empresa
                $documento = "getNit";
                $nombre = "getRazsoc";
            }
            if ($tipopc == 17) { //visarempresa
                $documento = "getNit";
                $nombre = "getRazsoc";
            }

            if ($tipopc == 3) { //conyuge
                $documento = "getCedcon";
                $nombre = "getNombre";
            }
            //if($tipopc==4 || $tipopc==13 || $tipopc==6 || $tipopc==14){//beneficiario
            if ($tipopc == 4) { //beneficiario
                $documento = "getDocumento";
                $nombre = "getNombre";
            }
            if ($tipopc == 5) { //basicos
                $documento = "getDocumento";
                $nombre = "getDocumentoDetalle";
                $extra = $mmercurio->getCampoDetalle() . " - " . $mmercurio->getAntval() . " - " . $mmercurio->getValor();
            }
            if ($tipopc == 7) { //retiro
                $documento = "getCedtra";
                $nombre = "getNomtra";
            }
            if ($tipopc == 8) { //certificiados
                $documento = "getCodben";
                $nombre = "getNombre";
                $extra = $mmercurio->getNomcer();
            }
            $gener02 = $this->Gener02->findFirst("usuario = '{$mmercurio->getUsuario()}'");
            if ($gener02 == false) $gener02 = new Gener02();
            $mercurio20 = $this->Mercurio20->findFirst("log = '{$mmercurio->getLog()}'");
            if ($mercurio20 == false) $mercurio20 = new Mercurio20();
            $dias_vencidos = $this->generalService->getDiasHabiles($tipopc, $mmercurio->getId());

            // $dias_vencidos = parent::calculaDias($tipopc,$mmercurio->getId());
            $html .= "<tr>";
            $html .= "<tr>";
            $html .= "<td>{$mmercurio->$documento()}</td>";
            $html .= "<td>{$mmercurio->$nombre()}</td>";
            $html .= "<td>{$gener02->getNombre()}</td>";
            $html .= "<td>{$mercurio20->getFecha()}</td>";
            $html .= "<td>{$dias_vencidos}</td>";
            if ($tipopc == "8" || $tipopc == "5") $html .= "<td>$extra</td>";
            $html .= "<td>{$mmercurio->getEstadoDetalle()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action' data-toggle='tooltip' data-original-title='Info' onclick=\"info('$tipopc','{$mmercurio->getId()}')\">";
            $html .= "<i class='fas fa-info'></i>";
            $html .= "</a>";
            $html .= "</td>";
            $html .= "</tr>";
            $html .= "</tr>";
        }
        return $this->renderText(json_encode($html));
    }

    public function reporte_auditoriaAction(Request $request)
    {
        $this->setResponse('view');
        $ciudad = $request->input("ciudad");
        $fecini = $request->input("fecini");
        $fecfin = $request->input("fecfin");
        $fecha = new \DateTime();
        $file = "public/temp/" . "reporte_beneficiariofoninez_" . $fecha->format('Y-m-d') . ".xls";
        require_once "Library/Excel/Main.php";
        $excels = new Spreadsheet_Excel_Writer($file);
        $excel = $excels->addWorksheet();
        $column_title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 12,
            'fgcolor' => 12,
            'border' => 1,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 13,
            'border' => 0,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $column_style = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 11,
            'border' => 1,
            'bordercolor' => 'black',
        ));
        //$excel->setMerge(0,1,0,6);
        $excel->setMerge(20, 20, 20, 20);
        $excel->write(0, 1, 'Reporte De Beneficiarios', $title);
        $columns = array("Colegio", 'Profesor', 'Modalidad', 'Documento Beneficiario', 'Nombres y Apellidos', 'Ciudad de residencia', 'Fecha Ingreso', 'Estado', 'Motivo');
        $excel->setColumn(0, 0, 80);
        $excel->setColumn(1, 1, 40);
        $excel->setColumn(2, 2, 70);
        $excel->setColumn(3, 3, 60);
        $excel->setColumn(4, 4, 80);
        $excel->setColumn(5, 5, 30);
        $excel->setColumn(6, 6, 30);
        $excel->setColumn(7, 7, 30);
        $excel->setColumn(8, 8, 30);
        $excel->setColumn(9, 9, 100);
        $i = 0;
        $j = 2;
        foreach ($columns as $column) {
            $value = ucfirst($column);
            $excel->write($j, $i++, $value, $column_title);
        }
        $j++;
        $condi = " and mercurio80.fecha>='$fecini' and mercurio80.fecha<='$fecfin' ";
        $datos_captura = $this->generalService->webService("datosconsultafoninez", array("sql" => "select id,xml4b085.detins as coddan from mercurio81,xml4b085 where codinf='$ciudad' and xml4b085.codins=mercurio81.coddan;"));
        $datos_capturac = $datos_captura['data'];

        if (!empty($datos_capturac)) {
            foreach ($datos_capturac['result'] as $datac) {
                $datos_capturaid = $this->generalService->webService("datosconsultafoninez", array("sql" => "select id from mercurio80 where colegio='{$datac['id']}' $condi;"));
                $datos_capturaid = $datos_capturaid['data'];
                $beneficiario = "";
                if (!empty($datos_capturaid)) {
                    $cont = count($datos_capturaid['result']);
                    foreach ($datos_capturaid['result'] as $data) {
                        if ($cont > 1) {
                            $beneficiario .= $data["id"] . ",";
                        } else {
                            $beneficiario .= $data["id"];
                        }
                        $cont--;
                    }
                    $datos_capturaevent = $this->generalService->webService("datosconsultafoninez", array("sql" => "select beneficiario,evento from mercurio84 where evento in($beneficiario) group by beneficiario,evento;"));
                    $datos_capturaevent = $datos_capturaevent['data'];
                    if (!empty($datos_capturaevent)) {

                        foreach ($datos_capturaevent['result'] as $mmercurio) {
                            $datos_capturaben = $this->generalService->webService("datosconsultafoninez", array("sql" => "select numideben,prinomben,segnomben,priapeben,segapeben,fecina,xml4b062.nombre as ciuresben,evento,profesor,modain,modjec,motivo,fecha from mercurio80,mercurio83,mercurio84,xml4b062 where mercurio83.id='{$mmercurio['beneficiario']}' and xml4b062.divpol=mercurio83.ciuresben and mercurio84.beneficiario='{$mmercurio['beneficiario']}' and mercurio80.id='{$mmercurio['evento']}'"));

                            //throw new DebugException(0);
                            if (!empty($datos_capturaben)) {
                                foreach ($datos_capturaben['data']['result'] as $mercurio) {
                                    $i = 0;
                                    $datos_pro = $this->generalService->webService("datosconsultafoninez", array("sql" => "select nombre from mercurio82 where id='{$mercurio['profesor']}'"));

                                    //throw new DebugException(0);
                                    $datos_modalidad = $this->generalService->webService("datosconsultafoninez", array("sql" => "select nombre from xml4b042 where modain='{$mercurio['modain']}'"));
                                    $datos_modalidad2 = $this->generalService->webService("datosconsultafoninez", array("sql" => "select nombre from xml4b050 where modjec='{$mercurio['modjec']}'"));


                                    //throw new DebugException(0);
                                    $excel->write($j, $i++, ($datac["coddan"]), $column_style);
                                    $excel->write($j, $i++, $datos_pro['data']['result'][0][0], $column_style);
                                    if (isset($datos_modalidad['data']['result'][0][0])) {
                                        $excel->write($j, $i++, $datos_modalidad['data']['result'][0][0], $column_style);
                                    } else {
                                        $excel->write($j, $i++, $datos_modalidad2['data']['result'][0][0], $column_style);
                                    }
                                    $excel->write($j, $i++, $mercurio["numideben"], $column_style);
                                    $excel->write($j, $i++, $mercurio["prinomben"] . " " . $mercurio["segnomben"] . " " . $mercurio["priapeben"] . " " . $mercurio["segapeben"], $column_style);
                                    $excel->write($j, $i++, $mercurio["ciuresben"], $column_style);
                                    $excel->write($j, $i++, $mercurio["fecha"], $column_style);
                                    if ($mercurio["fecina"] == "") {
                                        $excel->write($j, $i++, 'ACTIVO', $column_style);
                                    } else {
                                        $excel->write($j, $i++, 'INACTIVO', $column_style);
                                    }
                                    $excel->write($j, $i++, $mercurio["motivo"], $column_style);

                                    $j++;
                                }
                            }
                        }
                    }
                }
            }
        }
        $excels->close();
        header("location: " . env('APP_URL') . "/{$file}");
    }


    public function consulta_jec_viewAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Consulta Beneficiarios JEC");
        # Tag::setDocumentTitle('Consulta Beneficiarios JEC');
    }

    public function reporte_jecAction(Request $request)
    {
        $this->setResponse('view');
        $fecini = $request->input("fecini");
        $fecfin = $request->input("fecfin");
        $fecha = new \DateTime();
        $file = "public/temp/" . "reporte_beneficiariosjec_" . $fecha->format('Y-m-d') . ".xls";
        require_once "Library/Excel/Main.php";
        $excels = new Spreadsheet_Excel_Writer($file);
        $excel = $excels->addWorksheet();
        $column_title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 12,
            'fgcolor' => 12,
            'border' => 1,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 13,
            'border' => 0,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $column_style = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 11,
            'border' => 1,
            'bordercolor' => 'black',
        ));
        //$excel->setMerge(0,1,0,6);
        $excel->setMerge(20, 20, 20, 20);
        $excel->write(0, 1, 'Reporte De Beneficiarios', $title);
        $columns = array('Documento Beneficiario', 'Nombres y Apellidos', 'Fecha Nacimiento', 'Modalidad Escuela Formacion', 'Municipio de residencia', 'Codigo Infraestructura', 'Colegio', 'Area Geografica', 'Documento Profesor', 'Nombre Profesor', 'Fecha Corte');
        $excel->setColumn(0, 0, 40);
        $excel->setColumn(1, 1, 80);
        $excel->setColumn(2, 2, 70);
        $excel->setColumn(3, 3, 60);
        $excel->setColumn(4, 4, 80);
        $excel->setColumn(5, 5, 80);
        $excel->setColumn(6, 6, 80);
        $excel->setColumn(7, 7, 30);
        $excel->setColumn(8, 8, 30);
        $excel->setColumn(9, 9, 80);
        $excel->setColumn(10, 10, 40);

        $i = 0;
        $j = 2;
        foreach ($columns as $column) {
            $value = ucfirst($column);
            $excel->write($j, $i++, $value, $column_title);
        }
        $j++;
        $datos_captura = $this->generalService->webService("datosconsultafoninez", array("sql" => "select * from mercurio81,mercurio82,mercurio83,mercurio80,mercurio84 where mercurio84.beneficiario=mercurio83.id and mercurio84.evento=mercurio80.id and mercurio80.profesor=mercurio82.id and mercurio80.colegio=mercurio81.id and mercurio80.modjec is not null  and mercurio80.modjec!='' and mercurio80.fecha>='$fecini' and mercurio80.fecha<='$fecfin';"));
        $datos_capturac = $datos_captura['data'];
        if (!empty($datos_capturac)) {

            foreach ($datos_capturac['result'] as $mmercurio) {
                $ciudad = $this->generalService->webService("datosconsultafoninez", array("sql" => "select xml4b062.nombre as ciuresben from mercurio83,xml4b062 where mercurio83.id='{$mmercurio['beneficiario']}' and xml4b062.divpol=mercurio83.ciuresben"));

                $modalidad = $this->generalService->webService("datosconsultafoninez", array("sql" => "select nombre from xml4b050 where modjec='{$mmercurio['modjec']}'"));

                $infra = $this->generalService->webService("datosconsultafoninez", array("sql" => "select codinf,nomcom from xml4d088 where codinf='{$mmercurio['codinf']}'"));

                $colegio = $this->generalService->webService("datosconsultafoninez", array("sql" => "select codins,detins from xml4b085 where codins='{$mmercurio['coddan']}'"));

                $i = 0;
                $excel->write($j, $i++, $mmercurio["numideben"], $column_style);
                $excel->write($j, $i++, $mmercurio["prinomben"] . " " . $mmercurio["segnomben"] . " " . $mmercurio["priapeben"] . " " . $mmercurio["segapeben"], $column_style);
                $excel->write($j, $i++, $mmercurio["fecnacben"], $column_style);
                $excel->write($j, $i++, $modalidad['data']['result'][0][0], $column_style);
                if (isset($ciudad['data']['result'][0][0])) {
                    $excel->write($j, $i++, $ciudad['data']['result'][0][0], $column_style);
                } else {
                    $excel->write($j, $i++, "No tiene ciudad relacionada", $column_style);
                }
                $excel->write($j, $i++, $infra['data']['result'][0][0] . "-" . $infra['data']['result'][0][1], $column_style);
                $excel->write($j, $i++, $colegio['data']['result'][0][0] . "-" . $colegio['data']['result'][0][1], $column_style);
                if ($mmercurio["codareresben"] == "2") {
                    $excel->write($j, $i++, "RURAL", $column_style);
                } else {
                    $excel->write($j, $i++, "URBANA", $column_style);
                }
                $excel->write($j, $i++, $mmercurio["numdoc"], $column_style);
                $excel->write($j, $i++, $mmercurio["nombre"], $column_style);
                $excel->write($j, $i++, $mmercurio["fecha"], $column_style);
                $j++;
            }
        }

        $excels->close();
        header("location: " . env('APP_URL') . "/{$file}");
    }

    public function consulta_aipi_viewAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Consulta Beneficiarios AIPI");
        # Tag::setDocumentTitle('Consulta Beneficiarios AIPI');
    }

    public function reporte_aipiAction(Request $request)
    {
        $this->setResponse('view');
        $fecini = $request->input("fecini");
        $fecfin = $request->input("fecfin");
        $fecha = new \DateTime();
        $file = "public/temp/" . "reporte_beneficiariosaipi_" . $fecha->format('Y-m-d') . ".xls";
        require_once "Library/Excel/Main.php";
        $excels = new Spreadsheet_Excel_Writer($file);
        $excel = $excels->addWorksheet();
        $column_title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 12,
            'fgcolor' => 12,
            'border' => 1,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 13,
            'border' => 0,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $column_style = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 11,
            'border' => 1,
            'bordercolor' => 'black',
        ));
        //$excel->setMerge(0,1,0,6);
        $excel->setMerge(20, 20, 20, 20);
        $excel->write(0, 1, 'Reporte De Beneficiarios', $title);
        $columns = array('Documento Beneficiario', 'Nombres y Apellidos', 'Genero', 'Fecha Nacimiento', 'Municipio de residencia', 'Fecha Vinculacion', 'Fecha Inactivacion', 'Motivo', 'Fecha Corte');
        $excel->setColumn(0, 0, 40);
        $excel->setColumn(1, 1, 80);
        $excel->setColumn(2, 2, 30);
        $excel->setColumn(3, 3, 30);
        $excel->setColumn(4, 4, 40);
        $excel->setColumn(5, 5, 30);
        $excel->setColumn(6, 6, 30);
        $excel->setColumn(7, 7, 100);
        $excel->setColumn(8, 8, 30);


        $i = 0;
        $j = 2;
        foreach ($columns as $column) {
            $value = ucfirst($column);
            $excel->write($j, $i++, $value, $column_title);
        }
        $j++;
        $datos_captura = $this->generalService->webService("datosconsultafoninez", array("sql" => "select * from mercurio81,mercurio82,mercurio83,mercurio80,mercurio84 where mercurio84.beneficiario=mercurio83.id and mercurio84.evento=mercurio80.id and mercurio80.profesor=mercurio82.id and mercurio80.colegio=mercurio81.id and mercurio80.modain is not null and mercurio80.modain!='' and mercurio80.fecha>='$fecini' and mercurio80.fecha<='$fecfin';"));
        $datos_capturac = $datos_captura['data'];
        if (!empty($datos_capturac)) {

            foreach ($datos_capturac['result'] as $mmercurio) {
                $ciudad = $this->generalService->webService("datosconsultafoninez", array("sql" => "select xml4b062.nombre as ciuresben from mercurio83,xml4b062 where mercurio83.id='{$mmercurio['beneficiario']}' and xml4b062.divpol=mercurio83.ciuresben"));

                //throw new DebugException(0);
                $colegio = $this->generalService->webService("datosconsultafoninez", array("sql" => "select codins,detins from xml4b085 where codins='{$mmercurio['coddan']}'"));

                //throw new DebugException(0);
                $i = 0;
                $excel->write($j, $i++, $mmercurio["numideben"], $column_style);
                $excel->write($j, $i++, $mmercurio["prinomben"] . " " . $mmercurio["segnomben"] . " " . $mmercurio["priapeben"] . " " . $mmercurio["segapeben"], $column_style);
                if ($mmercurio["numideben"] == '1') {
                    $excel->write($j, $i++, "MASCULINO", $column_style);
                } else {
                    $excel->write($j, $i++, "FEMENINO", $column_style);
                }
                $excel->write($j, $i++, $mmercurio["fecnacben"], $column_style);
                if (isset($ciudad['data']['result'][0][0])) {
                    $excel->write($j, $i++, $ciudad['data']['result'][0][0], $column_style);
                } else {
                    $excel->write($j, $i++, "No tiene ciudad relacionada", $column_style);
                }
                $excel->write($j, $i++, $mmercurio["fecafiben"], $column_style);
                $excel->write($j, $i++, $mmercurio["fecina"], $column_style);
                $excel->write($j, $i++, $mmercurio["motivo"], $column_style);
                $excel->write($j, $i++, $mmercurio["fecha"], $column_style);
                $j++;
            }
        }

        $excels->close();
        header("location: " . env('APP_URL') . "/{$file}");
    }

    public function consulta_general_viewAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Consulta Beneficiarios General");
        #Tag::setDocumentTitle('Consulta Beneficiarios General');
    }

    public function reporte_generalAction(Request $request)
    {
        $this->setResponse('view');
        $fecini = $request->input("fecini");
        $fecfin = $request->input("fecfin");
        $fecha = new \DateTime();
        $file = "public/temp/" . "reporte_beneficiarios_" . $fecha->format('Y-m-d') . ".xls";
        require_once "Library/Excel/Main.php";
        $excels = new Spreadsheet_Excel_Writer($file);
        $excel = $excels->addWorksheet();
        $column_title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 12,
            'fgcolor' => 12,
            'border' => 1,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 13,
            'border' => 0,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $column_style = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 11,
            'border' => 1,
            'bordercolor' => 'black',
        ));
        //$excel->setMerge(0,1,0,6);
        $excel->setMerge(20, 20, 20, 20);
        $excel->write(0, 1, 'Reporte De Beneficiarios', $title);
        $columns = array(
            'Documento Profesor',
            'Nombre Profesor',
            'Colegio',
            'Modalidad Escuela Formacion',
            'Documento Beneficiario',
            'Nombres y Apellidos',
            'Estado',
            'Motivo',
            'Fecha Ingreso',
            'Municipio Residencia',
            'Fecha Corte',
            'Cedula Acudiente',
            'Nombres Acudiente',
            'Telefono Acudiente'
        );
        $excel->setColumn(0, 0, 40);
        $excel->setColumn(1, 1, 80);
        $excel->setColumn(2, 2, 100);
        $excel->setColumn(3, 3, 60);
        $excel->setColumn(4, 4, 40);
        $excel->setColumn(5, 5, 80);
        $excel->setColumn(6, 6, 30);
        $excel->setColumn(7, 7, 100);
        $excel->setColumn(8, 8, 30);
        $excel->setColumn(9, 9, 80);
        $excel->setColumn(10, 10, 30);
        $excel->setColumn(11, 11, 30);
        $excel->setColumn(12, 12, 80);
        $excel->setColumn(13, 13, 20);

        $i = 0;
        $j = 2;
        foreach ($columns as $column) {
            $value = ucfirst($column);
            $excel->write($j, $i++, $value, $column_title);
        }

        $j++;
        $datos_captura = $this->generalService->webService(
            "datosconsultafoninez",
            array("sql" =>
            "SELECT *
                FROM mercurio80
                INNER JOIN mercurio81 ON mercurio81.id = mercurio80.colegio
                INNER JOIN mercurio82 ON mercurio82.id = mercurio80.profesor
                INNER JOIN mercurio84 ON mercurio84.evento = mercurio80.id
                INNER JOIN mercurio83 ON mercurio83.id = mercurio84.beneficiario
                LEFT JOIN mercurio85 ON mercurio83.id = mercurio85.id
                WHERE
                mercurio80.fecha >= '{$fecini}' and mercurio80.fecha <= '{$fecfin}'
            ")
        );

        $datos_capturac = $datos_captura['data'];

        if (!empty($datos_capturac)) {

            foreach ($datos_capturac['result'] as $mmercurio) {

                $ciudad = $this->generalService->webService("datosconsultafoninez", array("sql" => "select xml4b062.nombre as ciuresben from mercurio83,xml4b062 where mercurio83.id='{$mmercurio['beneficiario']}' and xml4b062.divpol=mercurio83.ciuresben"));


                $modalidad = $this->generalService->webService("datosconsultafoninez", array("sql" => "select nombre from xml4b050 where modjec='{$mmercurio['modjec']}'"));



                $colegio = $this->generalService->webService("datosconsultafoninez", array("sql" => "select codins,detins from xml4b085 where codins='{$mmercurio['coddan']}'"));

                $i = 0;
                $excel->write($j, $i++, $mmercurio["numdoc"], $column_style);
                $excel->write($j, $i++, $mmercurio["nombre"], $column_style);
                $excel->write($j, $i++, $colegio['data']['result'][0][0] . "-" . $colegio['data']['result'][0][1], $column_style);
                if (isset($modalidad['data']['result'])) {
                    $excel->write($j, $i++, $modalidad['data']['result'][0][0], $column_style);
                } else {
                    $excel->write($j, $i++, "", $column_style);
                }

                $excel->write($j, $i++, $mmercurio["numideben"], $column_style);
                $excel->write($j, $i++, $mmercurio["prinomben"] . " " . $mmercurio["segnomben"] . " " . $mmercurio["priapeben"] . " " . $mmercurio["segapeben"], $column_style);
                if ($mmercurio["fecina"] == "") {
                    $excel->write($j, $i++, 'ACTIVO', $column_style);
                } else {
                    $excel->write($j, $i++, 'INACTIVO', $column_style);
                }

                $excel->write($j, $i++, $mmercurio["motivo"], $column_style);
                $excel->write($j, $i++, $mmercurio["fecha"], $column_style);
                if (isset($ciudad['data']['result'][0][0])) {
                    $excel->write($j, $i++, $ciudad['data']['result'][0][0], $column_style);
                } else {
                    $excel->write($j, $i++, "No tiene ciudad relacionada", $column_style);
                }

                $excel->write($j, $i++, $mmercurio["fecha"], $column_style);
                $excel->write($j, $i++, $mmercurio["numideacu"], $column_style);
                $excel->write($j, $i++, $mmercurio["prinomacu"] . ' ' . $mmercurio["segnomacu"] . ' ' . $mmercurio["priapeacu"] . ' ' . $mmercurio["segapeacu"], $column_style);
                $excel->write($j, $i++, $mmercurio["telacu"], $column_style);
                $j++;
            }
        }

        $excels->close();
        header("location: " . env('APP_URL') . "/{$file}");
    }
    public function infoAction(Request $request)
    {
        $this->setResponse("ajax");
        $tipopc = $request->input('tipopc');
        $id = $request->input('id');
        $response = "";
        $result = $this->generalService->consultaTipopc($tipopc, "info", $id);
        $response = $result['consulta'];
        return $this->renderText(json_encode($response));
    }


    public function carga_laboralAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Carga Laboral");
        $this->setParamToView("buttons", array("P" => array("btyp" => "btn-neutral", "func" => "reporte_excel_carga_laboral()", "glyp" => "fas fa-file-contract", "valr" => "Reporte")));
        $gener02 = $this->Gener02->findAllBySql("select distinct gener02.usuario,gener02.nombre,gener02.login from gener02,mercurio08 where gener02.usuario=mercurio08.usuario");
        $mercurio09 = $this->Mercurio09->find();

        $html = "<div class='card-body'>";
        $html .= "<div class='card-columns'>";

        foreach ($gener02 as $mgener02) {
            $html .= "<div class='card'>";
            $html .= "<div class='card-header bg-transparent text-center'>";
            $html .= "<h5 class='h4 ls-1 py-0 mb-0'>{$mgener02->getNombre()}</h5>";
            $html .= "</div>";
            $html .= "<ul class='list-group list-group-flush'>";
            $mercurio00 = $this->Mercurio08->find("usuario='{$mgener02->getUsuario()}'");
            foreach ($mercurio00 as $mmercurio08) {
                $condi = "estado in ('V','P')";
                $result = $this->generalService->consultaTipopc($mmercurio08->getTipopc(), 'count', "", $mgener02->getUsuario(), $condi);
                $count = isset($result['count']) ? $result['count'] : 0;
                $html .= "<li class='list-group-item d-flex justify-content-between align-items-center py-2'>";
                $html .= "<small>" . ucwords(strtolower($mmercurio08->getMercurio09()->getDetalle())) . "</small>";
                $html .= "<span class='badge badge-md badge-primary badge-pill'>$count</span>";
                $html .= "</li>";
            }
            /*foreach($mercurio09 as $mmercurio09){
                $condi = "estado='P'";
                $result = $this->generalService->consultaTipopc($mmercurio09->getTipopc(),'count',"",$mgener02->getUsuario(),$condi);
                $count = isset($result['count']) ? $result['count'] : 0;

            	$html .= "<li class='list-group-item d-flex justify-content-between align-items-center py-2'>";
            	$html .= "<small>".ucwords(strtolower($mmercurio09->getDetalle()))."</small>";
            	$html .= "<span class='badge badge-md badge-primary badge-pill'>$count</span>";
            	$html .= "</li>";
            } */
            $html .= "</ul>";
            $html .= "</div>";
        }

        $html .= "</div>";
        $html .= "</div>";

        $this->setParamToView("html", $html);
    }

    public function bkp_carga_laboralAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Carga Laboral");
        #Tag::setDocumentTitle('Carga Laboral');
        $this->setParamToView("buttons", array("P" => array("btyp" => "btn-neutral", "func" => "reporte_excel_carga_laboral()", "glyp" => "fas fa-file-contract", "valr" => "Reporte")));

        $html = "<div class='table-responsive'> ";
        $html .= "<table class='table'>";
        $html .= "<tr>";
        $html .= "<td>Usuario/Movimiento</td>";
        $mercurio09 = $this->Mercurio09->find();
        foreach ($mercurio09 as $mmercurio09) {
            $html .= "<td>{$mmercurio09->getDetalle()}</td>";
        }
        $html .= "</tr>";
        $gener02 = $this->Gener02->findAllBySql("select distinct gener02.usuario,gener02.nombre,gener02.login from gener02,mercurio08 where gener02.usuario=mercurio08.usuario");
        foreach ($gener02 as $mgener02) {
            $html .= "<tr>";
            $html .= "<td>{$mgener02->getNombre()}</td>";
            foreach ($mercurio09 as $mmercurio09) {
                $condi = "estado='P'";
                $result = $this->generalService->consultaTipopc($mmercurio09->getTipopc(), 'count', "", $mgener02->getUsuario(), $condi);
                $count = $result['count'];
                $html .= "<td>{$count}</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</table>";
        $html .= "</div>";
        $this->setParamToView("html", $html);
    }

    public function reporte_excel_carga_laboralAction()
    {
        $this->setResponse('view');
        $fecha = new \DateTime();
        $file = "public/temp/" . "reporte_carga_laboral" . $fecha->format('Y-m-d') . ".xls";
        require_once "Library/Excel/Main.php";
        $excels = new Spreadsheet_Excel_Writer($file);
        $excel = $excels->addWorksheet();
        $column_title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 12,
            'fgcolor' => 12,
            'border' => 1,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 13,
            'border' => 0,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $column_style = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 11,
            'border' => 1,
            'bordercolor' => 'black',
        ));
        $excel->setMerge(0, 1, 0, 6);
        $excel->write(0, 1, 'Reporte De Carga Laboral', $title);
        $excel->setColumn(1, 20, 45);
        $i = 0;
        $j = 2;
        $excel->write($j, $i++, "Usuario/Movimiento", $column_title);
        $mercurio09 = $this->Mercurio09->find();
        foreach ($mercurio09 as $mmercurio09) {
            $excel->write($j, $i++, $mmercurio09->getDetalle(), $column_title);
        }
        $j++;
        $gener02 = $this->Gener02->findAllBySql("select distinct gener02.usuario,gener02.nombre,gener02.login from gener02,mercurio08 where gener02.usuario=mercurio08.usuario");
        foreach ($gener02 as $mgener02) {
            $i = 0;
            $excel->write($j, $i++, $mgener02->getNombre(), $column_style);
            foreach ($mercurio09 as $mmercurio09) {
                $condi = "estado='P'";
                $result = $this->generalService->consultaTipopc($mmercurio09->getTipopc(), 'count', "", $mgener02->getUsuario(), $condi);
                $count = $result['count'];
                $excel->write($j, $i++, $count, $column_style);
            }
            $j++;
        }
        $excels->close();
        header("location: " . env('APP_URL') . "/{$file}");
    }

    public function reporte_excel_indicadoresAction($fecini, $fecfin)
    {
        $this->setResponse('view');
        $fecha = new \DateTime();
        $file = "public/temp/" . "reporte_indicadores" . $fecha->format('Y-m-d') . ".xls";
        require_once "Library/Excel/Main.php";
        $excels = new Spreadsheet_Excel_Writer($file);
        $excel = $excels->addWorksheet();
        $column_title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 12,
            'fgcolor' => 12,
            'border' => 1,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $title = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 13,
            'border' => 0,
            'bordercolor' => 'black',
            "halign" => 'center'
        ));
        $column_style = $excels->addFormat(array(
            'fontfamily' => 'Verdana',
            'size' => 11,
            'border' => 1,
            'bordercolor' => 'black',
        ));
        $excel->setMerge(0, 1, 0, 6);
        $excel->write(0, 1, 'Reporte De Indicadores', $title);
        $excel->setColumn(0, 0, 45);
        $estados = new Mercurio31();
        $estados = $estados->getEstadoArray();
        $i = 0;
        $j = 2;
        $excel->write($j, $i++, "Usuario/Movimiento", $column_title);
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
        $excel->write($j, $i++, "", $column_style);
        foreach ($mercurio09 as $mmercurio09) {
            foreach ($estados as $mestados) {
                $excel->write($j, $i++, $mestados, $column_style);
            }
            $excel->write($j, $i++, "TOT", $column_style);
            $excel->write($j, $i++, "VEN", $column_style);
        }
        $j++;
        $gener02 = $this->Gener02->findAllBySql("select distinct gener02.usuario,gener02.nombre,gener02.login from gener02,mercurio08 where gener02.usuario=mercurio08.usuario");
        foreach ($gener02 as $mgener02) {
            $i = 0;
            $excel->write($j, $i++, $mgener02->getNombre(), $column_style);
            foreach ($mercurio09 as $mmercurio09) {
                $valores_estado = "";
                $total_estado = 0;
                foreach ($estados as $key => $mestados) {
                    $condi = "estado='$key' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin'";
                    $result = $this->generalService->consultaTipopc($mmercurio09->getTipopc(), 'count', "", $mgener02->getUsuario(), $condi);
                    $count = $result['count'];
                    $excel->write($j, $i++, $count, $column_style);
                    $total_estado += $result['count'];
                }
                $condi = "estado<>'T' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin'";
                $result = $this->generalService->consultaTipopc($mmercurio09->getTipopc(), 'count', "", $mgener02->getUsuario(), $condi);
                $mercurio = $result['all'];
                $total_vencido = 0;
                foreach ($mercurio as $mmercurio) {
                    $dias_vencidos = $this->generalService->calculaDias($mmercurio09->getTipopc(), $mmercurio->getId());
                    if ($dias_vencidos > $mmercurio09->getDias()) $total_vencido++;
                }
                $excel->write($j, $i++, $total_estado, $column_style);
                $excel->write($j, $i++, $total_vencido, $column_style);
            }
            $j++;
        }
        $excels->close();
        header("location: " . env('APP_URL') . "/{$file}");
    }

    public function indicadoresAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Carga Laboral");
        $this->setParamToView("buttons", array("P" => array("btyp" => "btn-neutral", "func" => "reporte_excel_indicadores()", "glyp" => "fas fa-file-contract", "valr" => "Reporte")));
    }

    public function consulta_indicadoresAction(Request $request)
    {
        $this->setResponse("ajax");
        $fecini = $request->input("fecini");
        $fecfin = $request->input("fecfin");
        $mercurio09 = $this->Mercurio09->find();

        $html = "<div class='table-responsive'>";
        $html .= "<table class='table table-bordered table-hover table-sm'>";
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th>Usuario</th>";
        $html .= "<th>Acc\Estado</th>";

        $estados = new Mercurio31();
        $estados = $estados->getEstadoArray();

        foreach ($estados as $mestados) {
            $html .= "<th class='text-center'>";
            $html .= "$mestados ";
            $html .= "</th>";
        }
        $html .= "<th class='text-center'>";
        $html .= "TOT";
        $html .= "</th>";
        $html .= "<th class='text-center'>VEN</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        $gener02 = $this->Gener02->findAllBySql("select distinct gener02.usuario,gener02.nombre,gener02.login from gener02,mercurio08 where gener02.usuario=mercurio08.usuario");
        foreach ($gener02 as $mgener02) {
            $first = true;
            foreach ($mercurio09 as $mmercurio09) {
                $html .= "<tr>";
                if ($first) $html .= "<th class='align-middle' rowspan='" . count($mercurio09) . "'>{$mgener02->getNombre()}</th>";
                $first = false;
                $html .= "<td>{$mmercurio09->getDetalle()}</td>";

                $valores_estado = "";
                $total_estado = 0;
                foreach ($estados as $key => $mestados) {
                    $condi = "estado='$key' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin'";
                    $result = $this->generalService->consultaTipopc($mmercurio09->getTipopc(), 'count', "", $mgener02->getUsuario(), $condi);
                    $html .= "<td align='center'>";
                    $html .= "{$result['count']}";
                    $html .= "</td>";
                    $total_estado += $result['count'];
                }
                $condi = "estado<>'T' and mercurio20.fecha>='$fecini' and mercurio20.fecha<='$fecfin'";
                $result = $this->generalService->consultaTipopc($mmercurio09->getTipopc(), 'count', "", $mgener02->getUsuario(), $condi);
                $mercurio = $result['all'];
                $total_vencido = 0;
                foreach ($mercurio as $mmercurio) {
                    $dias_vencidos = $this->generalService->calculaDias($mmercurio09->getTipopc(), $mmercurio->getId());
                    if ($dias_vencidos > $mmercurio09->getDias()) $total_vencido++;
                }
                $html .= "<td align='center'>$total_estado</td>";
                $html .= "<td align='center'>$total_vencido</td>";

                $html .= "</tr>";
            }
        }
        $html .= "</tbody>";
        $html .= "</table>";
        $html .= "</div>";
        $this->renderText(json_encode($html));
    }
}
