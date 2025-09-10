<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio65Controller extends ApplicationController
{

    private $query = "1=1";
    private $cantidad_pagina = 0;

    public function initialize()
    {
        Core::importLibrary("Services", "Services");
        $this->setTemplateAfter('main');
        $this->setPersistance(true);
        $this->cantidad_pagina = $this->numpaginate;
        Services::Init();
    }

    public function showTabla($paginate)
    {
        $html  = '<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">';
        $html .= "<thead class='thead-light'>";
        $html .= "<tr>";
        $html .= "<th scope='col'>Nit</th>";
        $html .= "<th scope='col'>Razon Social</th>";
        $html .= "<th scope='col'>Direccion</th>";
        $html .= "<th scope='col'>Email</th>";
        $html .= "<th scope='col'>Clasificacion</th>";
        $html .= "<th scope='col'>Estado</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getNit()}</td>";
            $html .= "<td>{$mtable->getRazsoc()}</td>";
            $html .= "<td>{$mtable->getDireccion()}</td>";
            $html .= "<td>{$mtable->getEmail()}</td>";
            $html .= "<td>{$mtable->getCodclaDetalle()}</td>";
            $html .= "<td>{$mtable->getEstadoDetalle()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' onclick='editar(\"{$mtable->getCodsed()}\")'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' onclick='borrar(\"{$mtable->getCodsed()}\")'>";
            $html .= "<i class='fas fa-trash text-white'></i>";
            $html .= "</a>";
            $html .= "</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        return $html;
    }

    public function aplicarFiltroAction()
    {
        $this->setResponse("ajax");
        $consultasOldServices = new GeneralService();
        $this->query = $consultasOldServices->converQuery();
        self::buscarAction();
    }

    public function changeCantidadPaginaAction()
    {
        $this->setResponse("ajax");
        $this->cantidad_pagina = $this->getPostParam("numero");
        self::buscarAction();
    }

    public function indexAction()
    {
        $campo_field = array(
            "nit" => "Nit",
            "razsoc" => "Razon Social",
            "estado" => "Estado",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("buttons", array("N", "F", "R"));
        $this->setParamToView("title", "Comercios");
        Tag::setDocumentTitle('Comercios');
    }


    public function buscarAction()
    {
        $this->setResponse("ajax");
        $pagina = $this->getPostParam('pagina');
        if ($pagina == "") $pagina = 1;
        $paginate = Tag::paginate($this->Mercurio65->find("$this->query"), $pagina, $this->cantidad_pagina);
        $html = self::showTabla($paginate);
        $consultasOldServices = new GeneralService();
        $html_paginate = $consultasOldServices->showPaginate($paginate);
        $response['consulta'] = $html;
        $response['paginate'] = $html_paginate;
        $this->renderObject($response, false);
    }

    public function editarAction()
    {
        try {
            $this->setResponse("ajax");
            $codsed = $this->getPostParam('codsed');
            $mercurio65 = $this->Mercurio65->findFirst("codsed = '$codsed'");
            if ($mercurio65 == false) $mercurio65 = new Mercurio65();
            $this->renderObject($mercurio65->getArray(), false);
        } catch (DbException $e) {
            parent::setLogger($e->getMessage());
            parent::ErrorTrans();
        }
    }

    public function borrarAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $codsed = $this->getPostParam('codsed');
                $modelos = array("Mercurio65");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $this->Mercurio65->deleteAll("codsed = '$codsed'");
                parent::finishTrans();
                $response = parent::successFunc("Borrado Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                parent::ErrorTrans();
            }
        } catch (TransactionFailed $e) {
            $response = parent::errorFunc("No se puede Borrar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function guardarAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $codsed = $this->getPostParam('codsed', "addslaches", "extraspaces", "striptags");
                $nit = $this->getPostParam('nit', "addslaches", "extraspaces", "striptags");
                $razsoc = $this->getPostParam('razsoc', "addslaches", "extraspaces", "striptags");
                $direccion = $this->getPostParam('direccion', "addslaches", "extraspaces", "striptags");
                $email = $this->getPostParam('email', "addslaches", "extraspaces", "striptags");
                $celular = $this->getPostParam('celular', "addslaches", "extraspaces", "striptags");
                $codcla = $this->getPostParam('codcla', "addslaches", "alpha", "extraspaces", "striptags");
                $detalle = $this->getPostParam('detalle', "addslaches", "alpha", "extraspaces", "striptags");
                $estado = $this->getPostParam('estado', "addslaches", "alpha", "extraspaces", "striptags");
                $lat = $this->getPostParam('lat');
                $log = $this->getPostParam('log');
                $modelos = array("Mercurio65");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $mercurio65 = new Mercurio65();
                $mercurio65->setTransaction($Transaccion);
                $mercurio65->setCodsed($codsed);
                $mercurio65->setNit($nit);
                $mercurio65->setRazsoc($razsoc);
                $mercurio65->setDireccion($direccion);
                $mercurio65->setEmail($email);
                $mercurio65->setCelular($celular);
                $mercurio65->setCodcla($codcla);
                $mercurio65->setDetalle($detalle);
                $mercurio65->setLat($lat);
                $mercurio65->setLog($log);
                $mercurio65->setEstado($estado);
                $mercurio01 = $this->Mercurio01->findFirst();
                if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != "") {
                    $extension = explode(".", $_FILES['archivo']['name']);
                    $name = $codsed . "_comercios." . end($extension);
                    $_FILES['archivo']['name'] = $name;
                    $estado = $this->uploadFile("archivo", $mercurio01->getPath());
                    if ($estado != false) {
                        $mercurio65->setArchivo($name);
                        if (!$mercurio65->save()) {
                            parent::setLogger($mercurio65->getMessages());
                            parent::ErrorTrans();
                        }
                        $response = parent::successFunc("Se adjunto con exito el archivo");
                    } else {
                        $response = parent::errorFunc("No se cargo: Tamano del archivo muy grande o No es Valido");
                    }
                } else {
                    $response = parent::errorFunc("No se cargo el archivo");
                }
                parent::finishTrans();
                $response = parent::successFunc("Creacion Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                parent::ErrorTrans();
            }
        } catch (TransactionFailed $e) {
            $response = parent::errorFunc("No se puede guardar/editar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function validePkAction()
    {
        try {
            $this->setResponse("ajax");
            $nit = $this->getPostParam('nit', "addslaches", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio65->count("*", "conditions: nit = '$nit'");
            if ($l > 0) {
                $response = parent::errorFunc("El Registro ya se encuentra Digitado");
            }
            return $this->renderObject($response, false);
        } catch (DbException $e) {
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function reporteAction($format = 'P')
    {
        $this->setResponse("ajax");
        $_fields = array();
        $_fields["codsed"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["nit"] = array('header' => "Nit", 'size' => "15", 'align' => "C");
        $_fields["razsoc"] = array('header' => "Razon Social", 'size' => "15", 'align' => "C");
        $_fields["email"] = array('header' => "Email", 'size' => "31", 'align' => "C");
        $_fields["celular"] = array('header' => "Celular", 'size' => "31", 'align' => "C");
        $_fields["codcla"] = array('header' => "Clasificacion", 'size' => "31", 'align' => "C");
        $_fields["detalle"] = array('header' => "Detalle", 'size' => "31", 'align' => "C");
        $_fields["lat"] = array('header' => "Latitud", 'size' => "31", 'align' => "C");
        $_fields["log"] = array('header' => "Longitud", 'size' => "31", 'align' => "C");
        $_fields["estado"] = array('header' => "Estado", 'size' => "31", 'align' => "C");
        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio65", $_fields, $this->query, "Firmas", $format);
        return $this->renderObject($file, false);
    }
}
