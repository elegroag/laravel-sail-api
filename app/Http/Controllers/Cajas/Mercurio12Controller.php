<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio12Controller extends ApplicationController
{

    private $query = "1=1";
    private $cantidad_pagina = 10;

    public function initialize()
    {
        Core::importLibrary("Services", "Services");
        $this->setTemplateAfter('main');
        $this->setPersistance(true);
        $this->cantidad_pagina = $this->numpaginate;
        Services::Init();
    }

    public function beforeFilter($permisos = array())
    {
        $permisos = array("aplicarFiltro" => "22", "editar" => "23", "guardar" => "24", "buscar" => "25", "borrar" => "26");
        $flag = parent::beforeFilter($permisos);
        if (!$flag) {
            $response = parent::errorFunc("No cuenta con los permisos para este proceso");
            if (is_ajax()) {
                $this->setResponse("ajax");
                $this->renderObject($response, false);
            } else {
                $this->redirect("principal/index/0");
            }
            return false;
        }
    }


    public function showTabla($paginate)
    {
        $html  = '<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">';
        $html .= "<thead class='thead-light'>";
        $html .= "<tr>";
        $html .= "<th scope='col'>Documento</th>";
        $html .= "<th scope='col'>Detalle</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getCoddoc()}</td>";
            $html .= "<td>{$mtable->getDetalle()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' data-cid='{$mtable->getCoddoc()}' data-toggle='editar'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' data-cid='{$mtable->getCoddoc()}' data-toggle='borrar'>";
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
        return $this->buscarAction();
    }

    public function changeCantidadPaginaAction()
    {
        return $this->buscarAction();
    }

    public function indexAction()
    {
        $campo_field = array(
            "coddoc" => "Codigo",
            "detalle" => "Detalle",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Documentos");
        $this->setParamToView("buttons", array("N", "F", "R"));
        Tag::setDocumentTitle('Motivos Documentos');
    }


    public function buscarAction()
    {
        $this->setResponse("ajax");
        $consultasOldServices = new GeneralService();
        $this->query = $consultasOldServices->converQuery();

        $pagina = ($this->getPostParam('pagina')) ? $this->getPostParam('pagina') : 1;
        $this->cantidad_pagina = ($this->getPostParam("numero")) ? $this->getPostParam("numero") : 10;

        if ($pagina == "") $pagina = 1;

        $paginate = Tag::paginate($this->Mercurio12->find("$this->query"), $pagina, $this->cantidad_pagina);
        $html = self::showTabla($paginate);

        $consultasOldServices = new GeneralService();
        $html_paginate = $consultasOldServices->showPaginate($paginate, $event = 'toggle');

        $response['consulta'] = $html;
        $response['paginate'] = $html_paginate;
        return $this->renderObject($response, false);
    }

    public function editarAction()
    {
        try {
            $this->setResponse("ajax");
            $coddoc = $this->getPostParam('coddoc');
            $mercurio12 = $this->Mercurio12->findFirst("coddoc = '$coddoc'");
            if ($mercurio12 == false) $mercurio12 = new Mercurio12();

            return $this->renderObject($mercurio12->getArray(), false);
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
                $coddoc = $this->getPostParam('coddoc');
                $modelos = array("Mercurio12");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $this->Mercurio12->deleteAll("coddoc = '$coddoc'");
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
                $coddoc = $this->getPostParam('coddoc', "addslaches", "alpha", "extraspaces", "striptags");
                $detalle = $this->getPostParam('detalle', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("Mercurio12");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $mercurio12 = new Mercurio12();
                $mercurio12->setTransaction($Transaccion);
                $mercurio12->setCoddoc($coddoc);
                $mercurio12->setDetalle($detalle);
                if (!$mercurio12->save()) {
                    parent::setLogger($mercurio12->getMessages());
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
            $coddoc = $this->getPostParam('coddoc', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio12->count("*", "conditions: coddoc = '$coddoc'");
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
        $_fields["coddoc"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["detalle"] = array('header' => "Detalle", 'size' => "31", 'align' => "C");
        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio12", $_fields, $this->query, "Documentos", $format);
        return $this->renderObject($file, false);
    }
}
