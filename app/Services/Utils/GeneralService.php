<?php

namespace App\Services\Utils;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio02;
use App\Models\Mercurio04;
use App\Models\Mercurio05;
use App\Models\Mercurio08;
use App\Models\Mercurio20;
use App\Services\Api\PortalMercurio;
use Carbon\Carbon;

class GeneralService
{

    protected $numpaginate = 5;

    public function __construct() {}

    public function converserialize($str, $indice)
    {
        $data = array();
        $strArray = preg_split("/&/", $str);
        $i = 0;
        foreach ($strArray as $item) {
            $array = preg_split("/=/", $item);
            if (count($array) < 2) continue;
            $data[$i]["$indice"] = trim($array[1]);
            $i++;
        }
        return $data;
    }

    public function converQuery()
    {
        $campo = $this->converserialize($_POST['campo'], 'mcampo');
        $condi = $this->converserialize($_POST['condi'], 'mcondi');
        $value = $this->converserialize($_POST['value'], 'mvalue');
        $query = array();
        for ($i = 0; $i < count($campo); $i++) {
            $mcampo = $campo[$i]['mcampo'];
            $mcondi = $condi[$i]['mcondi'];
            $mvalue = $value[$i]['mvalue'];
            switch ($mcondi) {
                case "como":
                    $mcondi = "like";
                    break;
                case "igual":
                    $mcondi = "=";
                    break;
                case "mayor":
                    $mcondi = ">";
                    break;
                case "menor":
                    $mcondi = "<";
                    break;
                case "mayorigual":
                    $mcondi = ">=";
                    break;
                case "menorigual":
                    $mcondi = "<=";
                    break;
                case "diferente":
                    $mcondi = "<>";
                    break;
            }
            if ($mcondi == "like")
                $query[] = "$mcampo $mcondi '%$mvalue%'";
            else
                $query[] = "$mcampo $mcondi '$mvalue'";
        }
        $query = count($query) != 0 ? join(" AND ", $query) : " 1=1 ";
        return $query;
    }

    public function showPaginate($paginate)
    {
        $html = "<div class='row'>";
        $html .= "<div class='col-sm-12 col-md-auto mr-auto pr-0 d-none d-md-inline'>";
        $html .= "<label class='text-nowrap mb-0'>";
        $html .= "Mostrar ";
        $html .= "<select id='cantidad_paginate' name='cantidad_paginate' class='form-control form-control-sm d-sm-inline-block w-auto' onchange='changeCantidadPagina()'>";
        $html .= "<option value='5'>5</option>";
        $html .= "<option value='10'>10</option>";
        $html .= "<option value='30'>30</option>";
        $html .= "<option value='50'>50</option>";
        $html .= "<option value='100'>100</option>";
        $html .= "</select>";
        $html .= " registros";
        $html .= "</label>";
        $html .= "</div>";
        $html .= "<div class='col-sm-12 col-md-auto pl-0 pr-0 pr-sm-3'>";
        $html .= "<nav aria-label='...'>";
        $html .= "<ul class='pagination justify-content-center justify-content-md-end mb-0'> ";
        $html .= "<li class='page-item' onclick=\"buscar(this);\" pagina='{$paginate->first}'>";
        $html .= "<a class='page-link'><i class='fas fa-angle-double-left'></i></a>";
        $html .= "</li>";
        $html .= "<li class='page-item' onclick=\"buscar(this);\" pagina='{$paginate->before}'>";
        $html .= "<a class='page-link'><i class='fas fa-angle-left'></i></a>";
        $html .= "</li>";
        for ($i = $paginate->current - 5; $i < $paginate->current; $i++) {
            if ($i < $paginate->first) continue;
            $html .= "<li class='page-item' onclick=\"buscar(this);\"><a class='page-link'>" . $i . "</a></li>";
        }
        for ($i = $paginate->current; $i <= ($paginate->current + 5); $i++) {
            $class = "";
            if ($i == $paginate->current) $class = "active";
            if ($i > $paginate->last) continue;
            $html .= "<li class='page-item $class' onclick=\"buscar(this);\"><a class='page-link'>" . $i . "</a></li>";
        }
        $html .= "<li class='page-item' onclick=\"buscar(this);\" pagina='{$paginate->next}'>";
        $html .= "<a class='page-link'><i class='fas fa-angle-right'></i></a>";
        $html .= "</li>";
        $html .= "<li class='page-item' onclick=\"buscar(this);\" pagina='{$paginate->last}'>";
        $html .= "<a class='page-link'><i class='fas fa-angle-double-right'></i></a>";
        $html .= "</li>";
        $html .= "</ul>";
        $html .= "</nav>";
        $html .= "</div>";

        $html .= "</div>";
        return $html;
    }

    public function createReport($model, $_fields, $query = '1=1', $title = 'Reporte', $format = 'P') {}

    public function webService($funcion, $params)
    {
        $app = [
            "mode" => env('API_MODE'),
            "host_portal_dev" => env('HOST_PORTAL_DEV'),
            "host_portal_pro" => env('HOST_PORTAL_PRO'),
            "portal" => env('PORTAL')
        ];
        $portalMercurio =  new PortalMercurio(json_decode(json_encode($app), true));
        $portalMercurio->send(
            [
                'servicio' => $funcion,
                'params' => $params
            ]
        );

        if ($portalMercurio->isJson()) {
            return $portalMercurio->toArray();
        } else {
            return false;
        }
    }

    public function sendEmail2($correo, $nombre = '', $asunto, $msj, $file = '')
    {
        $mercurio02 = (new Mercurio02())->findFirst();
        $mcontenido  = "";
        $mcontenido .= "<div style='padding:0px;margin:0px'>";
        $mcontenido .= "<table width='100%' bgcolor='#EEEEEE' cellpadding='0' cellspacing='0' border='0'>";
        $mcontenido .= "<tbody>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td align='center' style='font-family:Helvetica,Arial;padding:0px'>";
        $mcontenido .= "<table width='100%' cellpadding='0' cellspacing='0' border='0' style='width:100%;max-width:690px'>";
        $mcontenido .= "<tbody>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td style='padding:0px'>";
        $mcontenido .= "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
        $mcontenido .= "<tbody>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td style='background: white;'>";
        $rutaImg = getcwd() . "/public/img/Mercurio/logob.png";
        $rutaImg = "http://186.119.116.228:8091/Mercurio/public/img/Mercurio/logob.png";
        $mcontenido .= "<img style='display:block;border:none' src='" . $rutaImg . "' width='30%' height='' title='Sistemas Y Solucuiones Integradas' alt='Sistemas y Soluciones Integradas'>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td bgcolor='#FFFFFF' style='padding:20px 20px 0;border: none;border-top:none;border-bottom:none'>";
        $mcontenido .= "<div style='font-family:Helvetica,Arial;font-size:22px;line-height:32px;color:#00638a'>&nbsp;</div>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td bgcolor='#FFFFFF' style='padding:15px 20px 25px;border: none;border-top:none;border-bottom:none'>";
        $mcontenido .= "<div style='font-family:Helvetica,Arial;font-size:14px;font-style:italic;color:black;'>";
        $mcontenido .= " <table align='center' width='100%' border='0'>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td bgcolor='#FFFFFF' style='padding:15px 20px 25px;border: none;border-top:none;border-bottom:none'>";
        $mcontenido .= "<div style='font-family:Helvetica,Arial;font-size:14px;font-style:italic;color:black;'>" . $msj . "</div>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "</table>";
        $mcontenido .= "</div>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "</tbody>";
        $mcontenido .= "</table>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td style='padding:0px;background:#fff;border:1px solid #e1e1e1;border-top:none;border-bottom:none'>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td valign='middle' style='padding:21px;background:#f5f5f5;border:1px solid #e1e1e1;border-top:1px solid #eee;border-bottom:1px solid #eeeeee;font-family:Helvetica,Arial;font-size:14px;font-style:italic;line-height:20px;color:#787878'>{$mercurio02->getRazsoc()} <br/>Direccion: {$mercurio02->getDireccion()} <br/>Email: {$mercurio02->getEmail()}  <br/>Telefono: {$mercurio02->getTelefono()}<br/><br/> Website: <a style='font-family:Helvetica,Arial;font-size:14px;line-height:20px;color:#478eae;text-decoration:none' href='http://{$mercurio02->getPagweb()}' target='_blank'>{$mercurio02->getPagweb()}</a>.";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td valign='middle'>";
        $mcontenido .= "<div style='background:#373737;border:1px solid #e1e1e1;border-top:none'>";
        $mcontenido .= "<table width='100%' cellpadding='0' cellspacing='0' border='0' style='padding:0 20px'>";
        $mcontenido .= "<tbody>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td height='50' valign='middle' align='left' style='font-family:Helvetica,Arial;font-size:11px;color:#8e8e8e'>Mercurio - Sistemas y Soluciones Integradas S.A.S - 2019</td>";
        $mcontenido .= "</tbody>";
        $mcontenido .= "</table>";
        $mcontenido .= "</div>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "</tbody>";
        $mcontenido .= "</table>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "</tbody>";
        $mcontenido .= "</table>";
        $mcontenido .= "</div>";

        $mercurio01 = Mercurio01::first();

        $senderEmail = new SenderEmail();
        $senderEmail->setters(
            "asunto: $asunto",
            "emisor_email: {$mercurio01->getEmail()}",
            "emisor_clave: {$mercurio01->getClave()}",
            "emisor_nombre: COMFACA"
        );

        $files = (is_array($file) == false) ? [$file] : $file;
        $senderEmail->send($correo, $mcontenido, $files);
        return true;
    }

    public function sendEmail($correo, $nombre = '', $asunto, $msj, $file = '')
    {
        $mercurio02 = Mercurio02::first();
        $mcontenido  = "";
        $mcontenido .= "<div style='padding:0px;margin:0px'>";
        $mcontenido .= "<table width='100%' bgcolor='#EEEEEE' cellpadding='0' cellspacing='0' border='0'>";
        $mcontenido .= "<tbody>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td align='center' style='font-family:Helvetica,Arial;padding:0px'>";
        $mcontenido .= "<table width='100%' cellpadding='0' cellspacing='0' border='0' style='width:100%;max-width:690px'>";
        $mcontenido .= "<tbody>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td style='padding:0px'>";
        $mcontenido .= "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
        $mcontenido .= "<tbody>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td style='background: white;'>";
        $rutaImg = getcwd() . "/public/img/Mercurio/logob.png";
        $rutaImg = "http://186.119.116.228:8091/Mercurio/public/img/Mercurio/logob.png";
        $mcontenido .= "<img style='display:block;border:none' src='" . $rutaImg . "' width='30%' height='' title='Sistemas Y Solucuiones Integradas' alt='Sistemas y Soluciones Integradas'>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td bgcolor='#FFFFFF' style='padding:20px 20px 0;border: none;border-top:none;border-bottom:none'>";
        $mcontenido .= "<div style='font-family:Helvetica,Arial;font-size:22px;line-height:32px;color:#00638a'>&nbsp;</div>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td bgcolor='#FFFFFF' style='padding:15px 20px 25px;border: none;border-top:none;border-bottom:none'>";
        $mcontenido .= "<div style='font-family:Helvetica,Arial;font-size:14px;font-style:italic;color:black;'>";
        $mcontenido .= " <table align='center' width='100%' border='0'>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td bgcolor='#FFFFFF' style='padding:15px 20px 25px;border: none;border-top:none;border-bottom:none'>";
        $mcontenido .= "<div style='font-family:Helvetica,Arial;font-size:14px;font-style:italic;color:black;'>" . $msj . "</div>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "</table>";
        $mcontenido .= "</div>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "</tbody>";
        $mcontenido .= "</table>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td style='padding:0px;background:#fff;border:1px solid #e1e1e1;border-top:none;border-bottom:none'>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td valign='middle' style='padding:21px;background:#f5f5f5;border:1px solid #e1e1e1;border-top:1px solid #eee;border-bottom:1px solid #eeeeee;font-family:Helvetica,Arial;font-size:14px;font-style:italic;line-height:20px;color:#787878'>{$mercurio02->getRazsoc()} <br/>Direccion: {$mercurio02->getDireccion()} <br/>Email: {$mercurio02->getEmail()}  <br/>Telefono: {$mercurio02->getTelefono()}<br/><br/> Website: <a style='font-family:Helvetica,Arial;font-size:14px;line-height:20px;color:#478eae;text-decoration:none' href='http://{$mercurio02->getPagweb()}' target='_blank'>{$mercurio02->getPagweb()}</a>.";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td valign='middle'>";
        $mcontenido .= "<div style='background:#373737;border:1px solid #e1e1e1;border-top:none'>";
        $mcontenido .= "<table width='100%' cellpadding='0' cellspacing='0' border='0' style='padding:0 20px'>";
        $mcontenido .= "<tbody>";
        $mcontenido .= "<tr>";
        $mcontenido .= "<td height='50' valign='middle' align='left' style='font-family:Helvetica,Arial;font-size:11px;color:#8e8e8e'>Mercurio - Sistemas y Soluciones Integradas S.A.S - 2019</td>";
        $mcontenido .= "</tbody>";
        $mcontenido .= "</table>";
        $mcontenido .= "</div>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "</tbody>";
        $mcontenido .= "</table>";
        $mcontenido .= "</td>";
        $mcontenido .= "</tr>";
        $mcontenido .= "</tbody>";
        $mcontenido .= "</table>";
        $mcontenido .= "</div>";

        $mercurio01 = Mercurio01::first();

        $senderEmail = new SenderEmail();
        $senderEmail->setters(
            "asunto: $asunto",
            "emisor_email: {$mercurio01->getEmail()}",
            "emisor_clave: {$mercurio01->getClave()}",
            "emisor_nombre: COMFACA"
        );

        $files = (is_array($file) == false) ? [$file] : $file;
        $senderEmail->send($correo, $mcontenido, $files);
        return true;
    }

    public function consultaEmpresa($mercurio30)
    {
        $tipopc = 2;
        $mercurio01 = \App\Models\Mercurio01::first();
        $mercurio37 = \App\Models\Mercurio37::where('tipopc', $tipopc)
            ->where('numero', $mercurio30->getId())
            ->first();


        $procesadorComando = Comman::Api();
        $procesadorComando->runPortal(array("servicio" => "captura_empresa", "params" => null));
        $datos_captura = $procesadorComando->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = array();
            foreach ($datos_captura['calemp'] as $data) $_calemp[$data['calemp']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_codact = array();
            foreach ($datos_captura['codact'] as $data) $_codact[$data['codact']] = $data['detalle'];
            $_tipsoc = array();
            foreach ($datos_captura['tipsoc'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }


        $col = "<div class='col-md-3 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nit</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getNit()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Razsoc</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getRazsoc()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Sigla</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getSigla()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Digito Verificacion</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getDigver()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Calidad Empresa</label>";
        $response .= "<p class='pl-2 description'>" . $_calemp[$mercurio30->getCalemp()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cedula Representante</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getCedrep()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombre Representante</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getRepleg()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Direccion de Notificacion</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getDireccion()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad de Notificacion</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio30->getCodciu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad donde realizan labores</lab>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio30->getCodzon()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Telefono de Notificacion</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getTelefono()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Celular de Notificacion</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getCelular()}</p>";
        $response .= "</div>";
        $response .= $col;
        //		$response .= "<label class='form-control-label'>Fax</label>";
        //		$response .= "<p class='pl-2 description'>{$mercurio30->getFax()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Email de Notificacion</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getEmail()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Actividad</label>";
        $response .= "<p class='pl-2 description'>" . $_codact[$mercurio30->getCodact()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Inicial</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getFecini()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Total Trabajadores</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getTottra()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Valor Nomina</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getValnom()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Sociedad</label>";
        $response .= "<p class='pl-2 description'>" . $_tipsoc[$mercurio30->getTipsoc()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Direccion Comercial</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getDirpri()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad Comercial</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getCiupri()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Telefono Comercial</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getCelpri()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Email Comercial</label>";
        $response .= "<p class='pl-2 description'>{$mercurio30->getEmailpri()}</p>";
        $response .= "</div>";

        $response .= "</div>";

        $response .= "<hr class='my-3'>";
        $response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
        $response .= "<div class='row pl-lg-4'>";


        foreach ($mercurio37 as $mmercurio37) {
            $mercurio12 = \App\Models\Mercurio12::where('coddoc', $mmercurio37->getCoddoc())->first();
            $response .= "<div class='btn-group col-md-4 mb-2'>";
            $response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
            $response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
            $response .= "</button>";
            $response .= "<button class='btn btn-icon btn-outline-danger ' type='button' onclick=\"borrarArchivo('{$mmercurio37->getNumero()}','{$mmercurio37->getCoddoc()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-trash'></i></span>";
            $response .= "</button>";
            $response .= "</div>";
        }
        $response .= "</div>";

        $response .= "<hr class='my-3'>";
        $response .= "<div class='row' style='overflow:scroll'>";
        $response .= "<table class='table table-bordered table-hover'>";
        $response .= "<thead>";
        $response .= "<tr>";
        $response .= "<th colspan=2>Seguimiento</th>";
        $response .= "</tr>";
        $response .= "<tr>";
        $response .= "<th>Observacion</th>";
        $response .= "<th>Fecha del Seguimiento</th>";
        $response .= "</tr>";
        $response .= "</thead>";
        $response .= "<tbody>";
        $mercurio10 = \App\Models\Mercurio10::where('tipopc', $tipopc)
            ->where('numero', $mercurio30->getId())
            ->orderBy('item', 'ASC')
            ->get();
        if ($mercurio10->count() == 0) {
            $response .= "<tr>";
            $response .= "<td colspan=2>NO HAY DATOS DE SEGUIMIENTO</td>";
            $response .= "</tr>";
        }
        foreach ($mercurio10 as $mmercurio10) {
            $response .= "<tr>";
            $response .= "<td>{$mmercurio10->getNota()}</td>";
            $response .= "<td>{$mmercurio10->getFecsis()}</td>";
            $response .= "</tr>";
        }
        $response .= "</tbody>";
        $response .= "</table>";
        $response .= "</div>";


        return $response;
    }

    public function consultaPensionado($mercurio38)
    {
        $tipopc = "9";
        $mercurio01 = \App\Models\Mercurio01::first();
        $mercurio37 = \App\Models\Mercurio37::where('tipopc', $tipopc)
            ->where('numero', $mercurio38->getId())
            ->get();


        $procesadorComando = Comman::Api();
        $procesadorComando->runPortal(array("servicio" => "captura_trabajador", "params" => null));
        $datos_captura = $procesadorComando->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) $_coddoc[$data['coddoc']] = $data['detalle'];
            $_sexo = array();
            foreach ($datos_captura['sexo'] as $data) $_sexo[$data['sexo']] = $data['detalle'];
            $_estciv = array();
            foreach ($datos_captura['estciv'] as $data) $_estciv[$data['estciv']] = $data['detalle'];
            $_cabhog = array();
            foreach ($datos_captura['cabhog'] as $data) $_cabhog[$data['cabhog']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_captra = array();
            foreach ($datos_captura['captra'] as $data) $_captra[$data['captra']] = $data['detalle'];
            $_tipdis = array();
            foreach ($datos_captura['tipdis'] as $data) $_tipdis[$data['tipdis']] = $data['detalle'];
            $_nivedu = array();
            foreach ($datos_captura['nivedu'] as $data) $_nivedu[$data['nivedu']] = $data['detalle'];
            $_rural = array();
            foreach ($datos_captura['rural'] as $data) $_rural[$data['rural']] = $data['detalle'];
            $_vivienda = array();
            foreach ($datos_captura['vivienda'] as $data) $_vivienda[$data['vivienda']] = $data['detalle'];
            $_tipafi = array();
            foreach ($datos_captura['tipafi'] as $data) $_tipafi[$data['tipafi']] = $data['detalle'];
        }

        $col = "<div class='col-md-4 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Trabajador</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cedula</label>";
        $response .= "<p class='pl-2 description'>{$mercurio38->getCedtra()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Apellidos</label>";
        $response .= "<p class='pl-2 description'>{$mercurio38->getPriape()} {$mercurio38->getSegape()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombres</label>";
        $response .= "<p class='pl-2 description'>{$mercurio38->getPrinom()} {$mercurio38->getSegnom()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Nacimiento</label>";
        $response .= "<p class='pl-2 description'>{$mercurio38->getFecnac()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad Nacimiento</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio38->getCodciu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Sexo</label>";
        $response .= "<p class='pl-2 description'>" . $_sexo[$mercurio38->getSexo()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Estado Civil</label>";
        $response .= "<p class='pl-2 description'>" . $_estciv[$mercurio38->getEstciv()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cabeza Hogar</label>";
        $response .= "<p class='pl-2 description'>" . $_cabhog[$mercurio38->getCabhog()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio38->getCodciu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Zona</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio38->getCodzon()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Direccion</label>";
        $response .= "<p class='pl-2 description'>{$mercurio38->getDireccion()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Barrio</label>";
        $response .= "<p class='pl-2 description'>{$mercurio38->getBarrio()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Telefono</label>";
        $response .= "<p class='pl-2 description'>{$mercurio38->getTelefono()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Celular</label>";
        $response .= "<p class='pl-2 description'>{$mercurio38->getCelular()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fax</label>";
        $response .= "<p class='pl-2 description'>{$mercurio38->getFax()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Email</label>";
        $response .= "<p class='pl-2 description'>{$mercurio38->getEmail()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Ingreso</label>";
        $response .= "<p class='pl-2 description'>{$mercurio38->getFecing()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Salario</label>";
        $response .= "<p class='pl-2 description'>{$mercurio38->getSalario()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Capcidad de trabajo</label>";
        $response .= "<p class='pl-2 description'>" . $_captra[$mercurio38->getCaptra()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Discapacidad</label>";
        $response .= "<p class='pl-2 description'>" . $_tipdis[$mercurio38->getTipdis()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nivel Educacion</label>";
        $response .= "<p class='pl-2 description'>" . $_nivedu[$mercurio38->getNivedu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Rural</label>";
        $response .= "<p class='pl-2 description'>" . $_rural[$mercurio38->getRural()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Vivienda</label>";
        $response .= "<p class='pl-2 description'>" . $_vivienda[$mercurio38->getVivienda()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Afiliado</label>";
        $response .= "<p class='pl-2 description'>" . $_tipafi[$mercurio38->getTipafi()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Autoriza</label>";
        $response .= "<p class='pl-2 description'>{$mercurio38->getAutoriza()}</p>";
        $response .= "</div>";

        $response .= "</div>";

        $response .= "<hr class='my-3'>";
        $response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
        $response .= "<div class='row pl-lg-4'>";


        foreach ($mercurio37 as $mmercurio37) {
            $mercurio12 = \App\Models\Mercurio12::where('coddoc', $mmercurio37->getCoddoc())->first();
            $response .= "<div class='btn-group col-md-4 mb-2'>";
            $response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
            $response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
            $response .= "</button>";
            $response .= "<button class='btn btn-icon btn-outline-danger ' type='button' onclick=\"borrarArchivo('{$mmercurio37->getNumero()}','{$mmercurio37->getCoddoc()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-trash'></i></span>";
            $response .= "</button>";
            $response .= "</div>";
        }
        $response .= "</div>";


        $response .= "<hr class='my-3'>";
        $response .= "<div class='row' style='overflow:scroll'>";
        $response .= "<table class='table table-bordered table-hover'>";
        $response .= "<thead>";
        $response .= "<tr>";
        $response .= "<th colspan=2>Seguimiento</th>";
        $response .= "</tr>";
        $response .= "<tr>";
        $response .= "<th>Observacion</th>";
        $response .= "<th>Fecha del Seguimiento</th>";
        $response .= "</tr>";
        $response .= "</thead>";
        $response .= "<tbody>";

        $mercurio10 = \App\Models\Mercurio10::where('tipopc', $tipopc)
            ->where('numero', $mercurio38->getId())
            ->orderBy('item', 'ASC')
            ->get();
        if ($mercurio10->count() == 0) {
            $response .= "<tr>";
            $response .= "<td colspan=2>NO HAY DATOS DE SEGUIMIENTO</td>";
            $response .= "</tr>";
        }
        foreach ($mercurio10 as $mmercurio10) {
            $response .= "<tr>";
            $response .= "<td>{$mmercurio10->getNota()}</td>";
            $response .= "<td>{$mmercurio10->getFecsis()}</td>";
            $response .= "</tr>";
        }
        $response .= "</tbody>";
        $response .= "</table>";
        $response .= "</div>";

        return $response;
    }

    public function consultaFacultativo($mercurio36)
    {
        $tipopc = "10";
        $mercurio01 = \App\Models\Mercurio01::first();
        $mercurio37 = \App\Models\Mercurio37::where('tipopc', $tipopc)
            ->where('numero', $mercurio36->getId())
            ->get();


        $procesadorComando = Comman::Api();
        $procesadorComando->runPortal(array("servicio" => "captura_trabajador", "params" => null));
        $datos_captura = $procesadorComando->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) $_coddoc[$data['coddoc']] = $data['detalle'];
            $_sexo = array();
            foreach ($datos_captura['sexo'] as $data) $_sexo[$data['sexo']] = $data['detalle'];
            $_estciv = array();
            foreach ($datos_captura['estciv'] as $data) $_estciv[$data['estciv']] = $data['detalle'];
            $_cabhog = array();
            foreach ($datos_captura['cabhog'] as $data) $_cabhog[$data['cabhog']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_captra = array();
            foreach ($datos_captura['captra'] as $data) $_captra[$data['captra']] = $data['detalle'];
            $_tipdis = array();
            foreach ($datos_captura['tipdis'] as $data) $_tipdis[$data['tipdis']] = $data['detalle'];
            $_nivedu = array();
            foreach ($datos_captura['nivedu'] as $data) $_nivedu[$data['nivedu']] = $data['detalle'];
            $_rural = array();
            foreach ($datos_captura['rural'] as $data) $_rural[$data['rural']] = $data['detalle'];
            $_vivienda = array();
            foreach ($datos_captura['vivienda'] as $data) $_vivienda[$data['vivienda']] = $data['detalle'];
            $_tipafi = array();
            foreach ($datos_captura['tipafi'] as $data) $_tipafi[$data['tipafi']] = $data['detalle'];
        }

        $col = "<div class='col-md-4 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Trabajador</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cedula</label>";
        $response .= "<p class='pl-2 description'>{$mercurio36->getCedtra()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Apellidos</label>";
        $response .= "<p class='pl-2 description'>{$mercurio36->getPriape()} {$mercurio36->getSegape()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombres</label>";
        $response .= "<p class='pl-2 description'>{$mercurio36->getPrinom()} {$mercurio36->getSegnom()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Nacimiento</label>";
        $response .= "<p class='pl-2 description'>{$mercurio36->getFecnac()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad Nacimiento</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio36->getCodciu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Sexo</label>";
        $response .= "<p class='pl-2 description'>" . $_sexo[$mercurio36->getSexo()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Estado Civil</label>";
        $response .= "<p class='pl-2 description'>" . $_estciv[$mercurio36->getEstciv()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cabeza Hogar</label>";
        $response .= "<p class='pl-2 description'>" . $_cabhog[$mercurio36->getCabhog()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio36->getCodciu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Zona</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio36->getCodzon()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Direccion</label>";
        $response .= "<p class='pl-2 description'>{$mercurio36->getDireccion()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Barrio</label>";
        $response .= "<p class='pl-2 description'>{$mercurio36->getBarrio()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Telefono</label>";
        $response .= "<p class='pl-2 description'>{$mercurio36->getTelefono()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Celular</label>";
        $response .= "<p class='pl-2 description'>{$mercurio36->getCelular()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fax</label>";
        $response .= "<p class='pl-2 description'>{$mercurio36->getFax()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Email</label>";
        $response .= "<p class='pl-2 description'>{$mercurio36->getEmail()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Ingreso</label>";
        $response .= "<p class='pl-2 description'>{$mercurio36->getFecing()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Salario</label>";
        $response .= "<p class='pl-2 description'>{$mercurio36->getSalario()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Capcidad de trabajo</label>";
        $response .= "<p class='pl-2 description'>" . $_captra[$mercurio36->getCaptra()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Discapacidad</label>";
        $response .= "<p class='pl-2 description'>" . $_tipdis[$mercurio36->getTipdis()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nivel Educacion</label>";
        $response .= "<p class='pl-2 description'>" . $_nivedu[$mercurio36->getNivedu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Rural</label>";
        $response .= "<p class='pl-2 description'>" . $_rural[$mercurio36->getRural()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Vivienda</label>";
        $response .= "<p class='pl-2 description'>" . $_vivienda[$mercurio36->getVivienda()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Afiliado</label>";
        $response .= "<p class='pl-2 description'>" . $_tipafi[$mercurio36->getTipafi()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Autoriza</label>";
        $response .= "<p class='pl-2 description'>{$mercurio36->getAutoriza()}</p>";
        $response .= "</div>";
        $response .= "</div>";

        $response .= "<hr class='my-3'>";
        $response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
        $response .= "<div class='row pl-lg-4'>";


        foreach ($mercurio37 as $mmercurio37) {
            $mercurio12 = \App\Models\Mercurio12::where('coddoc', $mmercurio37->getCoddoc())->first();
            $response .= "<div class='btn-group col-md-4 mb-2'>";
            $response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
            $response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
            $response .= "</button>";
            $response .= "<button class='btn btn-icon btn-outline-danger ' type='button' onclick=\"borrarArchivo('{$mmercurio37->getNumero()}','{$mmercurio37->getCoddoc()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-trash'></i></span>";
            $response .= "</button>";
            $response .= "</div>";
        }
        $response .= "</div>";

        $response .= "<hr class='my-3'>";
        $response .= "<div class='row' style='overflow:scroll'>";
        $response .= "<table class='table table-bordered table-hover'>";
        $response .= "<thead>";
        $response .= "<tr>";
        $response .= "<th colspan=2>Seguimiento</th>";
        $response .= "</tr>";
        $response .= "<tr>";
        $response .= "<th>Observacion</th>";
        $response .= "<th>Fecha del Seguimiento</th>";
        $response .= "</tr>";
        $response .= "</thead>";
        $response .= "<tbody>";
        $mercurio10 = \App\Models\Mercurio10::where('tipopc', $tipopc)
            ->where('numero', $mercurio36->getId())
            ->orderBy('item', 'ASC')
            ->get();
        if ($mercurio10->count() == 0) {
            $response .= "<tr>";
            $response .= "<td colspan=2>NO HAY DATOS DE SEGUIMIENTO</td>";
            $response .= "</tr>";
        }
        foreach ($mercurio10 as $mmercurio10) {
            $response .= "<tr>";
            $response .= "<td>{$mmercurio10->getNota()}</td>";
            $response .= "<td>{$mmercurio10->getFecsis()}</td>";
            $response .= "</tr>";
        }
        $response .= "</tbody>";
        $response .= "</table>";
        $response .= "</div>";

        return $response;
    }

    public function consultaComunitaria($mercurio39)
    {
        $tipopc = "11";
        $mercurio01 = \App\Models\Mercurio01::first();
        $mercurio37 = \App\Models\Mercurio37::where('tipopc', $tipopc)
            ->where('numero', $mercurio39->getId())
            ->get();


        $procesadorComando = Comman::Api();
        $procesadorComando->runPortal(array("servicio" => "captura_trabajador", "params" => null));
        $datos_captura = $procesadorComando->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) $_coddoc[$data['coddoc']] = $data['detalle'];
            $_sexo = array();
            foreach ($datos_captura['sexo'] as $data) $_sexo[$data['sexo']] = $data['detalle'];
            $_estciv = array();
            foreach ($datos_captura['estciv'] as $data) $_estciv[$data['estciv']] = $data['detalle'];
            $_cabhog = array();
            foreach ($datos_captura['cabhog'] as $data) $_cabhog[$data['cabhog']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_captra = array();
            foreach ($datos_captura['captra'] as $data) $_captra[$data['captra']] = $data['detalle'];
            $_tipdis = array();
            foreach ($datos_captura['tipdis'] as $data) $_tipdis[$data['tipdis']] = $data['detalle'];
            $_nivedu = array();
            foreach ($datos_captura['nivedu'] as $data) $_nivedu[$data['nivedu']] = $data['detalle'];
            $_rural = array();
            foreach ($datos_captura['rural'] as $data) $_rural[$data['rural']] = $data['detalle'];
            $_vivienda = array();
            foreach ($datos_captura['vivienda'] as $data) $_vivienda[$data['vivienda']] = $data['detalle'];
            $_tipafi = array();
            foreach ($datos_captura['tipafi'] as $data) $_tipafi[$data['tipafi']] = $data['detalle'];
        }

        $col = "<div class='col-md-4 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Trabajador</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cedula</label>";
        $response .= "<p class='pl-2 description'>{$mercurio39->getCedtra()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Apellidos</label>";
        $response .= "<p class='pl-2 description'>{$mercurio39->getPriape()} {$mercurio39->getSegape()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombres</label>";
        $response .= "<p class='pl-2 description'>{$mercurio39->getPrinom()} {$mercurio39->getSegnom()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Nacimiento</label>";
        $response .= "<p class='pl-2 description'>{$mercurio39->getFecnac()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad Nacimiento</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio39->getCodciu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Sexo</label>";
        $response .= "<p class='pl-2 description'>" . $_sexo[$mercurio39->getSexo()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Estado Civil</label>";
        $response .= "<p class='pl-2 description'>" . $_estciv[$mercurio39->getEstciv()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cabeza Hogar</label>";
        $response .= "<p class='pl-2 description'>" . $_cabhog[$mercurio39->getCabhog()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio39->getCodciu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Zona</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio39->getCodzon()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Direccion</label>";
        $response .= "<p class='pl-2 description'>{$mercurio39->getDireccion()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Barrio</label>";
        $response .= "<p class='pl-2 description'>{$mercurio39->getBarrio()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Telefono</label>";
        $response .= "<p class='pl-2 description'>{$mercurio39->getTelefono()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Celular</label>";
        $response .= "<p class='pl-2 description'>{$mercurio39->getCelular()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fax</label>";
        $response .= "<p class='pl-2 description'>{$mercurio39->getFax()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Email</label>";
        $response .= "<p class='pl-2 description'>{$mercurio39->getEmail()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Ingreso</label>";
        $response .= "<p class='pl-2 description'>{$mercurio39->getFecing()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Salario</label>";
        $response .= "<p class='pl-2 description'>{$mercurio39->getSalario()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Capcidad de trabajo</label>";
        $response .= "<p class='pl-2 description'>" . $_captra[$mercurio39->getCaptra()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Discapacidad</label>";
        $response .= "<p class='pl-2 description'>" . $_tipdis[$mercurio39->getTipdis()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nivel Educacion</label>";
        $response .= "<p class='pl-2 description'>" . $_nivedu[$mercurio39->getNivedu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Rural</label>";
        $response .= "<p class='pl-2 description'>" . $_rural[$mercurio39->getRural()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Vivienda</label>";
        $response .= "<p class='pl-2 description'>" . $_vivienda[$mercurio39->getVivienda()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Afiliado</label>";
        $response .= "<p class='pl-2 description'>" . $_tipafi[$mercurio39->getTipafi()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Autoriza</label>";
        $response .= "<p class='pl-2 description'>{$mercurio39->getAutoriza()}</p>";
        $response .= "</div>";
        $response .= "</div>";

        $response .= "<hr class='my-3'>";
        $response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
        $response .= "<div class='row pl-lg-4'>";


        foreach ($mercurio37 as $mmercurio37) {
            $mercurio12 = \App\Models\Mercurio12::where('coddoc', $mmercurio37->getCoddoc())->first();
            $response .= "<div class='btn-group col-md-4 mb-2'>";
            $response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
            $response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
            $response .= "</button>";
            $response .= "<button class='btn btn-icon btn-outline-danger ' type='button' onclick=\"borrarArchivo('{$mmercurio37->getNumero()}','{$mmercurio37->getCoddoc()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-trash'></i></span>";
            $response .= "</button>";
            $response .= "</div>";
        }
        $response .= "</div>";

        $response .= "<hr class='my-3'>";
        $response .= "<div class='row' style='overflow:scroll'>";
        $response .= "<table class='table table-bordered table-hover'>";
        $response .= "<thead>";
        $response .= "<tr>";
        $response .= "<th colspan=2>Seguimiento</th>";
        $response .= "</tr>";
        $response .= "<tr>";
        $response .= "<th>Observacion</th>";
        $response .= "<th>Fecha del Seguimiento</th>";
        $response .= "</tr>";
        $response .= "</thead>";
        $response .= "<tbody>";
        $mercurio10 = \App\Models\Mercurio10::where('tipopc', $tipopc)
            ->where('numero', $mercurio39->getId())
            ->orderBy('item', 'ASC')
            ->get();
        if ($mercurio10->count() == 0) {
            $response .= "<tr>";
            $response .= "<td colspan=2>NO HAY DATOS DE SEGUIMIENTO</td>";
            $response .= "</tr>";
        }
        foreach ($mercurio10 as $mmercurio10) {
            $response .= "<tr>";
            $response .= "<td>{$mmercurio10->getNota()}</td>";
            $response .= "<td>{$mmercurio10->getFecsis()}</td>";
            $response .= "</tr>";
        }
        $response .= "</tbody>";
        $response .= "</table>";
        $response .= "</div>";

        return $response;
    }

    public function consultaDomestico($mercurio40)
    {
        $tipopc = "12";
        $mercurio01 = \App\Models\Mercurio01::first();
        $mercurio37 = \App\Models\Mercurio37::where('tipopc', $tipopc)
            ->where('numero', $mercurio40->getId())
            ->first();


        $procesadorComando = Comman::Api();
        $procesadorComando->runPortal(array("servicio" => "captura_trabajador", "params" => null));
        $datos_captura = $procesadorComando->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) $_coddoc[$data['coddoc']] = $data['detalle'];
            $_sexo = array();
            foreach ($datos_captura['sexo'] as $data) $_sexo[$data['sexo']] = $data['detalle'];
            $_estciv = array();
            foreach ($datos_captura['estciv'] as $data) $_estciv[$data['estciv']] = $data['detalle'];
            $_cabhog = array();
            foreach ($datos_captura['cabhog'] as $data) $_cabhog[$data['cabhog']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_captra = array();
            foreach ($datos_captura['captra'] as $data) $_captra[$data['captra']] = $data['detalle'];
            $_tipdis = array();
            foreach ($datos_captura['tipdis'] as $data) $_tipdis[$data['tipdis']] = $data['detalle'];
            $_nivedu = array();
            foreach ($datos_captura['nivedu'] as $data) $_nivedu[$data['nivedu']] = $data['detalle'];
            $_rural = array();
            foreach ($datos_captura['rural'] as $data) $_rural[$data['rural']] = $data['detalle'];
            $_vivienda = array();
            foreach ($datos_captura['vivienda'] as $data) $_vivienda[$data['vivienda']] = $data['detalle'];
            $_tipafi = array();
            foreach ($datos_captura['tipafi'] as $data) $_tipafi[$data['tipafi']] = $data['detalle'];
        }

        $col = "<div class='col-md-4 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Trabajador</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cedula</label>";
        $response .= "<p class='pl-2 description'>{$mercurio40->getCedtra()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Apellidos</label>";
        $response .= "<p class='pl-2 description'>{$mercurio40->getPriape()} {$mercurio40->getSegape()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombres</label>";
        $response .= "<p class='pl-2 description'>{$mercurio40->getPrinom()} {$mercurio40->getSegnom()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Nacimiento</label>";
        $response .= "<p class='pl-2 description'>{$mercurio40->getFecnac()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad Nacimiento</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio40->getCodciu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Sexo</label>";
        $response .= "<p class='pl-2 description'>" . $_sexo[$mercurio40->getSexo()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Estado Civil</label>";
        $response .= "<p class='pl-2 description'>" . $_estciv[$mercurio40->getEstciv()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cabeza Hogar</label>";
        $response .= "<p class='pl-2 description'>" . $_cabhog[$mercurio40->getCabhog()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio40->getCodciu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Zona</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio40->getCodzon()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Direccion</label>";
        $response .= "<p class='pl-2 description'>{$mercurio40->getDireccion()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Barrio</label>";
        $response .= "<p class='pl-2 description'>{$mercurio40->getBarrio()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Telefono</label>";
        $response .= "<p class='pl-2 description'>{$mercurio40->getTelefono()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Celular</label>";
        $response .= "<p class='pl-2 description'>{$mercurio40->getCelular()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fax</label>";
        $response .= "<p class='pl-2 description'>{$mercurio40->getFax()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Email</label>";
        $response .= "<p class='pl-2 description'>{$mercurio40->getEmail()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Ingreso</label>";
        $response .= "<p class='pl-2 description'>{$mercurio40->getFecing()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Salario</label>";
        $response .= "<p class='pl-2 description'>{$mercurio40->getSalario()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Capcidad de trabajo</label>";
        $response .= "<p class='pl-2 description'>" . $_captra[$mercurio40->getCaptra()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Discapacidad</label>";
        $response .= "<p class='pl-2 description'>" . $_tipdis[$mercurio40->getTipdis()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nivel Educacion</label>";
        $response .= "<p class='pl-2 description'>" . $_nivedu[$mercurio40->getNivedu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Rural</label>";
        $response .= "<p class='pl-2 description'>" . $_rural[$mercurio40->getRural()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Vivienda</label>";
        $response .= "<p class='pl-2 description'>" . $_vivienda[$mercurio40->getVivienda()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Afiliado</label>";
        $response .= "<p class='pl-2 description'>" . $_tipafi[$mercurio40->getTipafi()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Autoriza</label>";
        $response .= "<p class='pl-2 description'>{$mercurio40->getAutoriza()}</p>";
        $response .= "</div>";
        $response .= "</div>";

        $response .= "<hr class='my-3'>";
        $response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
        $response .= "<div class='row pl-lg-4'>";


        foreach ($mercurio37 as $mmercurio37) {
            $mercurio12 = \App\Models\Mercurio12::where('coddoc', $mmercurio37->getCoddoc())->first();
            $response .= "<div class='btn-group col-md-4 mb-2'>";
            $response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
            $response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
            $response .= "</button>";
            $response .= "<button class='btn btn-icon btn-outline-danger ' type='button' onclick=\"borrarArchivo('{$mmercurio37->getNumero()}','{$mmercurio37->getCoddoc()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-trash'></i></span>";
            $response .= "</button>";
            $response .= "</div>";
        }
        $response .= "</div>";

        $response .= "<hr class='my-3'>";
        $response .= "<div class='row' style='overflow:scroll'>";
        $response .= "<table class='table table-bordered table-hover'>";
        $response .= "<thead>";
        $response .= "<tr>";
        $response .= "<th colspan=2>Seguimiento</th>";
        $response .= "</tr>";
        $response .= "<tr>";
        $response .= "<th>Observacion</th>";
        $response .= "<th>Fecha del Seguimiento</th>";
        $response .= "</tr>";
        $response .= "</thead>";
        $response .= "<tbody>";
        $mercurio10 = \App\Models\Mercurio10::where('tipopc', $tipopc)->where('numero', $mercurio40->getId())->orderBy('item', 'asc')->get();
        if ($mercurio10->count() == 0) {
            $response .= "<tr>";
            $response .= "<td colspan=2>NO HAY DATOS DE SEGUIMIENTO</td>";
            $response .= "</tr>";
        }
        foreach ($mercurio10 as $mmercurio10) {
            $response .= "<tr>";
            $response .= "<td>{$mmercurio10->getNota()}</td>";
            $response .= "<td>{$mmercurio10->getFecsis()}</td>";
            $response .= "</tr>";
        }
        $response .= "</tbody>";
        $response .= "</table>";
        $response .= "</div>";

        return $response;
    }

    public function consultaTrabajador($mercurio31)
    {
        $tipopc = "1";
        $mercurio01 = \App\Models\Mercurio01::first();
        $mercurio37 = \App\Models\Mercurio37::where('tipopc', $tipopc)
            ->where('numero', $mercurio31->getId())
            ->get();



        $procesadorComando = Comman::Api();
        $procesadorComando->runPortal(array("servicio" => "captura_trabajador"));
        $datos_captura = $procesadorComando->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) $_coddoc[$data['coddoc']] = $data['detalle'];
            $_sexo = array();
            foreach ($datos_captura['sexo'] as $data) $_sexo[$data['sexo']] = $data['detalle'];
            $_estciv = array();
            foreach ($datos_captura['estciv'] as $data) $_estciv[$data['estciv']] = $data['detalle'];
            $_cabhog = array();
            foreach ($datos_captura['cabhog'] as $data) $_cabhog[$data['cabhog']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_captra = array();
            foreach ($datos_captura['captra'] as $data) $_captra[$data['captra']] = $data['detalle'];
            $_tipdis = array();
            foreach ($datos_captura['tipdis'] as $data) $_tipdis[$data['tipdis']] = $data['detalle'];
            $_nivedu = array();
            foreach ($datos_captura['nivedu'] as $data) $_nivedu[$data['nivedu']] = $data['detalle'];
            $_rural = array();
            foreach ($datos_captura['rural'] as $data) $_rural[$data['rural']] = $data['detalle'];
            $_tipcon = array();
            foreach ($datos_captura['tipcon'] as $data) $_tipcon[$data['tipcon']] = $data['detalle'];
            $_trasin = array();
            foreach ($datos_captura['trasin'] as $data) $_trasin[$data['trasin']] = $data['detalle'];
            $_vivienda = array();
            foreach ($datos_captura['vivienda'] as $data) $_vivienda[$data['vivienda']] = $data['detalle'];
            $_tipafi = array();
            foreach ($datos_captura['tipafi'] as $data) $_tipafi[$data['tipafi']] = $data['detalle'];
        }

        $col = "<div class='col-md-4 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Trabajador</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nit</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getNit()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Razsoc</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getRazsoc()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cedula</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getCedtra()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Apellidos</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getPriape()} {$mercurio31->getSegape()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombres</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getPrinom()} {$mercurio31->getSegnom()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Nacimiento</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getFecnac()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad Nacimiento</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio31->getCodciu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Sexo</label>";
        $response .= "<p class='pl-2 description'>" . $_sexo[$mercurio31->getSexo()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Estado Civil</label>";
        $response .= "<p class='pl-2 description'>" . $_estciv[$mercurio31->getEstciv()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cabeza Hogar</label>";
        $response .= "<p class='pl-2 description'>" . $_cabhog[$mercurio31->getCabhog()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio31->getCodciu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Zona</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio31->getCodzon()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Direccion</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getDireccion()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Barrio</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getBarrio()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Telefono</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getTelefono()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Celular</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getCelular()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fax</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getFax()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Email</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getEmail()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Ingreso</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getFecing()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Salario</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getSalario()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Capcidad de trabajo</label>";
        $response .= "<p class='pl-2 description'>" . $_captra[$mercurio31->getCaptra()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Discapacidad</label>";
        $response .= "<p class='pl-2 description'>" . $_tipdis[$mercurio31->getTipdis()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nivel Educacion</label>";
        $response .= "<p class='pl-2 description'>" . $_nivedu[$mercurio31->getNivedu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Rural</label>";
        $response .= "<p class='pl-2 description'>" . $_rural[$mercurio31->getRural()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Horas</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getHoras()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Contrato</label>";
        $response .= "<p class='pl-2 description'>" . $_tipcon[$mercurio31->getTipcon()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Vivienda</label>";
        $response .= "<p class='pl-2 description'>" . $_vivienda[$mercurio31->getVivienda()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Afiliado</label>";
        $response .= "<p class='pl-2 description'>" . $_tipafi[$mercurio31->getTipafi()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Profesion</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getProfesion()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cargo</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getCargo()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Autoriza</label>";
        $response .= "<p class='pl-2 description'>{$mercurio31->getAutoriza()}</p>";
        $response .= "</div>";

        $response .= "</div>";

        $response .= "<hr class='my-3'>";
        $response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
        $response .= "<div class='row pl-lg-4'>";


        foreach ($mercurio37 as $mmercurio37) {
            $mercurio12 = \App\Models\Mercurio12::where('coddoc', $mmercurio37->getCoddoc())->first();
            $response .= "<div class='btn-group col-md-4 mb-2'>";
            $response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
            $response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
            $response .= "</button>";
            $response .= "<button class='btn btn-icon btn-outline-danger ' type='button' onclick=\"borrarArchivo('{$mmercurio37->getNumero()}','{$mmercurio37->getCoddoc()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-trash'></i></span>";
            $response .= "</button>";
            $response .= "</div>";
        }
        $response .= "</div>";

        $response .= "<hr class='my-3'>";
        $response .= "<div class='row' style='overflow:scroll'>";
        $response .= "<table class='table table-bordered table-hover'>";
        $response .= "<thead>";
        $response .= "<tr>";
        $response .= "<th colspan=2>Seguimiento</th>";
        $response .= "</tr>";
        $response .= "<tr>";
        $response .= "<th>Observacion</th>";
        $response .= "<th>Fecha del Seguimiento</th>";
        $response .= "</tr>";
        $response .= "</thead>";
        $response .= "<tbody>";
        $mercurio10 = \App\Models\Mercurio10::where('tipopc', $tipopc)
            ->where('numero', $mercurio31->getId())
            ->orderBy('item', 'ASC')
            ->get();
        if ($mercurio10->count() == 0) {
            $response .= "<tr>";
            $response .= "<td colspan=2>NO HAY DATOS DE SEGUIMIENTO</td>";
            $response .= "</tr>";
        }
        foreach ($mercurio10 as $mmercurio10) {
            $response .= "<tr>";
            $response .= "<td>{$mmercurio10->getNota()}</td>";
            $response .= "<td>{$mmercurio10->getFecsis()}</td>";
            $response .= "</tr>";
        }
        $response .= "</tbody>";
        $response .= "</table>";
        $response .= "</div>";

        return $response;
    }

    public function consultaConyuge($mercurio32)
    {
        $tipopc = "3";
        $mercurio01 = \App\Models\Mercurio01::first();
        $mercurio37 = \App\Models\Mercurio37::where('tipopc', $tipopc)
            ->where('numero', $mercurio32->getId())
            ->first();


        $procesadorComando = Comman::Api();
        $procesadorComando->runPortal(array("servicio" => "captura_conyuge"));
        $datos_captura = $procesadorComando->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) $_coddoc[$data['coddoc']] = $data['detalle'];
            $_sexo = array();
            foreach ($datos_captura['sexo'] as $data) $_sexo[$data['sexo']] = $data['detalle'];
            $_estciv = array();
            foreach ($datos_captura['estciv'] as $data) $_estciv[$data['estciv']] = $data['detalle'];
            $_comper = array();
            foreach ($datos_captura['comper'] as $data) $_comper[$data['comper']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_nivedu = array();
            foreach ($datos_captura['nivedu'] as $data) $_nivedu[$data['nivedu']] = $data['detalle'];
            $_vivienda = array();
            foreach ($datos_captura['vivienda'] as $data) $_vivienda[$data['vivienda']] = $data['detalle'];
            $_codocu = array();
            foreach ($datos_captura['codocu'] as $data) $_codocu[$data['codocu']] = $data['detalle'];
            $_captra = array();
            foreach ($datos_captura['captra'] as $data) $_captra[$data['captra']] = $data['detalle'];
        }

        $col = "<div class='col-md-4 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Conyuge</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cedula</label>";
        $response .= "<p class='pl-2 description'>{$mercurio32->getCedcon()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Apellidos</label>";
        $response .= "<p class='pl-2 description'>{$mercurio32->getPriape()} {$mercurio32->getSegape()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombres</label>";
        $response .= "<p class='pl-2 description'>{$mercurio32->getPrinom()} {$mercurio32->getSegnom()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Nacimiento</label>";
        $response .= "<p class='pl-2 description'>{$mercurio32->getFecnac()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad Nacimiento</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio32->getCiunac()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Sexo</label>";
        $response .= "<p class='pl-2 description'>" . $_sexo[$mercurio32->getSexo()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Estado Civil</label>";
        $response .= "<p class='pl-2 description'>" . $_estciv[$mercurio32->getEstciv()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Companera permanente</label>";
        $response .= "<p class='pl-2 description'>" . $_comper[$mercurio32->getComper()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad Residencia</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio32->getCiures()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Zona</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio32->getCodzon()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Vivienda</label>";
        $response .= "<p class='pl-2 description'>" . $_vivienda[$mercurio32->getTipviv()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Direccion</label>";
        $response .= "<p class='pl-2 description'>{$mercurio32->getDireccion()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Barrio</label>";
        $response .= "<p class='pl-2 description'>{$mercurio32->getBarrio()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Telefono</label>";
        $response .= "<p class='pl-2 description'>{$mercurio32->getTelefono()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Celular</label>";
        $response .= "<p class='pl-2 description'>{$mercurio32->getCelular()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Email</label>";
        $response .= "<p class='pl-2 description'>{$mercurio32->getEmail()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nivel Educacion</label>";
        $response .= "<p class='pl-2 description'>" . $_nivedu[$mercurio32->getNivedu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Ingreso</label>";
        $response .= "<p class='pl-2 description'>{$mercurio32->getFecing()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ocupacion</label>";
        $response .= "<p class='pl-2 description'>" . $_codocu[$mercurio32->getCodocu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Salario</label>";
        $response .= "<p class='pl-2 description'>{$mercurio32->getSalario()}</p>";
        $response .= "</div>";

        $response .= "</div>";

        $response .= "<hr class='my-3'>";
        $response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
        $response .= "<div class='row pl-lg-4'>";


        foreach ($mercurio37 as $mmercurio37) {
            $mercurio12 = \App\Models\Mercurio12::where('coddoc', $mmercurio37->getCoddoc())->first();
            $response .= "<div class='btn-group col-md-4 mb-2'>";
            $response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
            $response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
            $response .= "</button>";
            $response .= "<button class='btn btn-icon btn-outline-danger ' type='button' onclick=\"borrarArchivo('{$mmercurio37->getNumero()}','{$mmercurio37->getCoddoc()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-trash'></i></span>";
            $response .= "</button>";
            $response .= "</div>";
        }
        $response .= "</div>";

        $response .= "<hr class='my-3'>";
        $response .= "<div class='row' style='overflow:scroll'>";
        $response .= "<table class='table table-bordered table-hover'>";
        $response .= "<thead>";
        $response .= "<tr>";
        $response .= "<th colspan=2>Seguimiento</th>";
        $response .= "</tr>";
        $response .= "<tr>";
        $response .= "<th>Observacion</th>";
        $response .= "<th>Fecha del Seguimiento</th>";
        $response .= "</tr>";
        $response .= "</thead>";
        $response .= "<tbody>";
        $mercurio10 = \App\Models\Mercurio10::where('tipopc', $tipopc)
            ->where('numero', $mercurio32->getId())
            ->orderBy('item', 'ASC')
            ->get();
        if ($mercurio10->count() == 0) {
            $response .= "<tr>";
            $response .= "<td colspan=2>NO HAY DATOS DE SEGUIMIENTO</td>";
            $response .= "</tr>";
        }
        foreach ($mercurio10 as $mmercurio10) {
            $response .= "<tr>";
            $response .= "<td>{$mmercurio10->getNota()}</td>";
            $response .= "<td>{$mmercurio10->getFecsis()}</td>";
            $response .= "</tr>";
        }
        $response .= "</tbody>";
        $response .= "</table>";
        $response .= "</div>";

        return $response;
    }

    public function consultaBeneficiario($mercurio34)
    {
        $tipopc = "4";
        $mercurio01 = \App\Models\Mercurio01::first();
        $mercurio37 = \App\Models\Mercurio37::where('tipopc', $tipopc)
            ->where('numero', $mercurio34->getId())
            ->first();

        $procesadorComando = Comman::Api();
        $procesadorComando->runPortal(array("servicio" => "captura_beneficiario"));
        $datos_captura = $procesadorComando->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) $_coddoc[$data['coddoc']] = $data['detalle'];
            $_sexo = array();
            foreach ($datos_captura['sexo'] as $data) $_sexo[$data['sexo']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_parent = array();
            foreach ($datos_captura['parent'] as $data) $_parent[$data['parent']] = $data['detalle'];
            $_huerfano = array();
            foreach ($datos_captura['huerfano'] as $data) $_huerfano[$data['huerfano']] = $data['detalle'];
            $_tiphij = array();
            foreach ($datos_captura['tiphij'] as $data) $_tiphij[$data['tiphij']] = $data['detalle'];
            $_nivedu = array();
            foreach ($datos_captura['nivedu'] as $data) $_nivedu[$data['nivedu']] = $data['detalle'];
            $_captra = array();
            foreach ($datos_captura['captra'] as $data) $_captra[$data['captra']] = $data['detalle'];
            $_tipdis = array();
            foreach ($datos_captura['tipdis'] as $data) $_tipdis[$data['tipdis']] = $data['detalle'];
            $_calendario = array();
            foreach ($datos_captura['calendario'] as $data) $_calendario[$data['calendario']] = $data['detalle'];
        }

        $col = "<div class='col-md-4 border-top border-right'>";

        dump("sss", $mercurio34->getCedcon());
        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Beneficiario</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Cedula Conyuge</label>";
        $response .= "<p class='pl-2 description'>{$mercurio34->getCedcon()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Documento</label>";
        $response .= "<p class='pl-2 description'>{$mercurio34->getDocumento()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Apellidos</label>";
        $response .= "<p class='pl-2 description'>{$mercurio34->getPriape()} {$mercurio34->getSegape()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombres</label>";
        $response .= "<p class='pl-2 description'>{$mercurio34->getPrinom()} {$mercurio34->getSegnom()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Nacimiento</label>";
        $response .= "<p class='pl-2 description'>{$mercurio34->getFecnac()}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Ciudad Nacimiento</label>";
        $response .= "<p class='pl-2 description'>" . $_codciu[$mercurio34->getCiunac()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Sexo</label>";
        $response .= "<p class='pl-2 description'>" . $_sexo[$mercurio34->getSexo()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Parent</label>";
        $response .= "<p class='pl-2 description'>" . $_parent[$mercurio34->getParent()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Huerfano</label>";
        $response .= "<p class='pl-2 description'>" . $_huerfano[$mercurio34->getHuerfano()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Hijo</label>";
        $response .= "<p class='pl-2 description'>" . $_tiphij[$mercurio34->getTiphij()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nivel Educacion</label>";
        $response .= "<p class='pl-2 description'>" . $_nivedu[$mercurio34->getNivedu()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Capacidad Trabajo</label>";
        $response .= "<p class='pl-2 description'>" . $_captra[$mercurio34->getCaptra()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Discapacidad</label>";
        $response .= "<p class='pl-2 description'>" . $_tipdis[$mercurio34->getTipdis()] . "</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Calendario</label>";
        $response .= "<p class='pl-2 description'>" . $_calendario[$mercurio34->getCalendario()] . "</p>";
        $response .= "</div>";
        $response .= "</div>";
        $response .= "<hr class='my-3'>";
        $response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
        $response .= "<div class='row pl-lg-4'>";

        foreach ($mercurio37 as $mmercurio37) {
            $mercurio12 = \App\Models\Mercurio12::where('coddoc', $mmercurio37->getCoddoc())->first();
            $response .= "<div class='btn-group col-md-4 mb-2'>";
            $response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
            $response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
            $response .= "</button>";
            $response .= "<button class='btn btn-icon btn-outline-danger ' type='button' onclick=\"borrarArchivo('{$mmercurio37->getNumero()}','{$mmercurio37->getCoddoc()}')\">";
            $response .= "<span class='btn-inner--icon'><i class='fas fa-trash'></i></span>";
            $response .= "</button>";
            $response .= "</div>";
        }
        $response .= "</div>";
        $response .= "<hr class='my-3'>";
        $response .= "<div class='row' style='overflow:scroll'>";
        $response .= "<table class='table table-bordered table-hover'>";
        $response .= "<thead>";
        $response .= "<tr>";
        $response .= "<th colspan=2>Seguimiento</th>";
        $response .= "</tr>";
        $response .= "<tr>";
        $response .= "<th>Observacion</th>";
        $response .= "<th>Fecha del Seguimiento</th>";
        $response .= "</tr>";
        $response .= "</thead>";
        $response .= "<tbody>";
        $mercurio10 = \App\Models\Mercurio10::where('tipopc', $tipopc)
            ->where('numero', $mercurio34->getId())
            ->orderBy('item', 'ASC')
            ->get();
        if ($mercurio10->count() == 0) {
            $response .= "<tr>";
            $response .= "<td colspan=2>NO HAY DATOS DE SEGUIMIENTO</td>";
            $response .= "</tr>";
        }
        foreach ($mercurio10 as $mmercurio10) {
            $response .= "<tr>";
            $response .= "<td>{$mmercurio10->getNota()}</td>";
            $response .= "<td>{$mmercurio10->getFecsis()}</td>";
            $response .= "</tr>";
        }
        $response .= "</tbody>";
        $response .= "</table>";
        $response .= "</div>";
        return $response;
    }

    public function asignarFuncionario($tipopc, $codciu)
    {
        $mercurio05 = (new Mercurio05)->findFirst("codciu = '$codciu'");
        if ($mercurio05 == false) {
            $mercurio04 = (new Mercurio04)->findFirst("principal='S'");
            $codofi = $mercurio04->getCodofi();
        } else {
            $codofi = $mercurio05->getCodofi();
        }
        $mercurio08 = (new Mercurio08)->findFirst("codofi = '$codofi' and tipopc='{$tipopc}' and orden='1'");
        if ($mercurio08 == false) {
            $usuario = (new Mercurio08)->minimum("usuario", "conditions: codofi = '{$codofi}' and tipopc='{$tipopc}' ");
        } else {
            $usuario = $mercurio08->getUsuario();
        }
        if ($usuario == "") return "";
        $usuario_orden = (new Mercurio08)->minimum("usuario", "conditions: codofi = '{$codofi}' and tipopc='{$tipopc}' and usuario > {$usuario}");
        Mercurio08::where('codofi', $codofi)
            ->where('tipopc', $tipopc)
            ->update(['orden' => '0']);
        Mercurio08::where('codofi', $codofi)
            ->where('tipopc', $tipopc)
            ->where('usuario', $usuario_orden)
            ->update(['orden' => '1']);
        return $usuario;
    }

    function startTrans($models) {}

    public function errorTrans($message = '',  $linea = '') {}

    public function finishTrans() {}
}
