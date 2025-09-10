<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio52Controller extends ApplicationController
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
        $html .= "<th scope='col'>Codigo</th>";
        $html .= "<th scope='col'>Detalle</th>";
        $html .= "<th scope='col'>Area</th>";
        $html .= "<th scope='col'>Url</th>";
        $html .= "<th scope='col'>Tipo</th>";
        $html .= "<th scope='col'>Estado</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getCodmen()}</td>";
            $html .= "<td>{$mtable->getDetalle()}</td>";
            $html .= "<td>{$mtable->getCodareDetalle()}</td>";
            $html .= "<td>{$mtable->getUrl()}</td>";
            $html .= "<td>{$mtable->getTipoDetalle()}</td>";
            $html .= "<td>{$mtable->getEstadoDetalle()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' onclick='editar(\"{$mtable->getCodmen()}\")'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' onclick='borrar(\"{$mtable->getCodmen()}\")'>";
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

    public function nuevoAction()
    {
        $this->setResponse("ajax");
        $numero = $this->Mercurio52->maximum("codmen") + 1;
        $response = parent::successFunc("ok", $numero);
        $this->renderObject($response, false);
    }

    public function indexAction()
    {
        $campo_field = array(
            "codmen" => "Codigo",
            "detalle" => "Detalle",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Menu");
        $this->setParamToView("buttons", array("N", "F", "R"));
        Tag::setDocumentTitle('Menu');
    }


    public function buscarAction()
    {
        $this->setResponse("ajax");
        $pagina = $this->getPostParam('pagina');
        if ($pagina == "") $pagina = 1;
        $paginate = Tag::paginate($this->Mercurio52->find("$this->query"), $pagina, $this->cantidad_pagina);
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
            $codmen = $this->getPostParam('codmen');
            $mercurio52 = $this->Mercurio52->findFirst("codmen = '$codmen'");
            if ($mercurio52 == false) $mercurio52 = new Mercurio52();
            $this->renderObject($mercurio52->getArray(), false);
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
                $codmen = $this->getPostParam('codmen');
                $modelos = array("Mercurio52");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $this->Mercurio52->deleteAll("codmen = '$codmen'");
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
                $codmen = $this->getPostParam('codmen', "addslaches", "alpha", "extraspaces", "striptags");
                $detalle = $this->getPostParam('detalle', "addslaches", "alpha", "extraspaces", "striptags");
                $codare = $this->getPostParam('codare', "addslaches", "alpha", "extraspaces", "striptags");
                $url = $this->getPostParam('url', "addslaches", "alpha", "extraspaces", "striptags");
                $tipo = $this->getPostParam('tipo', "addslaches", "alpha", "extraspaces", "striptags");
                $estado = $this->getPostParam('estado', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("Mercurio52");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $mercurio52 = new Mercurio52();
                $mercurio52->setTransaction($Transaccion);
                $mercurio52->setCodmen($codmen);
                $mercurio52->setDetalle($detalle);
                $mercurio52->setCodare($codare);
                $mercurio52->setUrl($url);
                $mercurio52->setTipo($tipo);
                $mercurio52->setEstado($estado);
                if (!$mercurio52->save()) {
                    parent::setLogger($mercurio52->getMessages());
                    parent::ErrorTrans();
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
            $codmen = $this->getPostParam('codmen', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio52->count("*", "conditions: codmen = '$codmen'");
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
        $_fields["codmen"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["detalle"] = array('header' => "Detalle", 'size' => "31", 'align' => "C");
        $_fields["codare"] = array('header' => "Area", 'size' => "31", 'align' => "C");
        $_fields["url"] = array('header' => "Url", 'size' => "31", 'align' => "C");
        $_fields["tipo"] = array('header' => "Tipo", 'size' => "31", 'align' => "C");
        $_fields["estado"] = array('header' => "Estado", 'size' => "31", 'align' => "C");
        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio52", $_fields, $this->query, "Menu", $format);
        return $this->renderObject($file, false);
    }
}
