<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio06Controller extends ApplicationController
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
        $permisos = array("aplicarFiltro" => "14", "editar" => "15", "guardar" => "16", "buscar" => "17", "borrar" => "18", "campo_view" => "19", "editarCampo" => "20", "borrarCampo" => "21");
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
        $html .= "<th scope='col'>Tipo</th>";
        $html .= "<th scope='col'>Detalle</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getTipo()}</td>";
            $html .= "<td>{$mtable->getDetalle()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-warning' title='Campos' data-toggle='campo-view' data-cid='{$mtable->getTipo()}' >";
            $html .= "<i class='fas fa-shield-alt text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' data-cid='{$mtable->getTipo()}' data-toggle='editar'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' data-cid='{$mtable->getTipo()}' data-toggle='borrar'>";
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
        $this->buscarAction();
    }

    public function changeCantidadPaginaAction()
    {
        $this->buscarAction();
    }

    public function indexAction()
    {
        $campo_field = array(
            "tipo" => "Codigo",
            "detalle" => "Detalle",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Tipos Acceso");
        $this->setParamToView("buttons", array("N", "F", "R"));
        Tag::setDocumentTitle('Motivos Tipos Acceso');
    }


    public function buscarAction()
    {
        $this->setResponse("ajax");
        $consultasOldServices = new GeneralService();
        $this->query = $consultasOldServices->converQuery();

        $pagina = ($this->getPostParam('pagina')) ? $this->getPostParam('pagina') : 1;
        $this->cantidad_pagina = ($this->getPostParam("numero")) ? $this->getPostParam("numero") : 10;

        $paginate = Tag::paginate($this->Mercurio06->find("$this->query"), $pagina, $this->cantidad_pagina);
        $html = self::showTabla($paginate);

        $consultasOldServices = new GeneralService();
        $html_paginate = $consultasOldServices->showPaginate($paginate, $event = 'toggle');

        $response['consulta'] = $html;
        $response['paginate'] = $html_paginate;
        $this->renderObject($response, false);
    }

    public function editarAction()
    {
        try {
            $this->setResponse("ajax");
            $tipo = $this->getPostParam('tipo');
            $mercurio06 = $this->Mercurio06->findFirst("tipo = '$tipo'");
            if ($mercurio06 == false) $mercurio06 = new Mercurio06();

            return $this->renderObject($mercurio06->getArray(), false);
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
                $tipo = $this->getPostParam('tipo');
                $modelos = array("Mercurio06");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $this->Mercurio06->deleteAll("tipo = '$tipo'");
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
                $tipo = $this->getPostParam('tipo', "addslaches", "alpha", "extraspaces", "striptags");
                $detalle = $this->getPostParam('detalle', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("Mercurio06");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $mercurio06 = new Mercurio06();
                $mercurio06->setTransaction($Transaccion);
                $mercurio06->setTipo($tipo);
                $mercurio06->setDetalle($detalle);
                if (!$mercurio06->save()) {
                    parent::setLogger($mercurio06->getMessages());
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
            $tipo = $this->getPostParam('tipo', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio06->count("*", "conditions: tipo = '$tipo'");
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

    public function validePkCampoAction()
    {
        try {
            $this->setResponse("ajax");
            $tipo = $this->getPostParam('tipo', "addslaches", "alpha", "extraspaces", "striptags");
            $campo = $this->getPostParam('campo_28', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio28->count("*", "conditions: tipo = '$tipo' and campo='$campo'");
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

    public function campo_viewAction()
    {
        try {
            $this->setResponse("ajax");
            $tipo = $this->getPostParam('tipo', "addslaches", "alpha", "extraspaces", "striptags");
            $response = "";
            $mercurio28 = $this->Mercurio28->find("tipo='$tipo'");
            foreach ($mercurio28 as $mmercurio28) {
                $value = "";
                $response .= "<tr>";
                $response .= "<td>" . $mmercurio28->getCampo() . "</td>";
                $response .= "<td>" . $mmercurio28->getDetalle() . "</td>";
                $response .= "<td>" . $mmercurio28->getOrden() . "</td>";
                $response .= "<td class='table-actions'>";
                $response .= "<a href='#!' class='table-action btn btn-xs btn-primary' data-toggle='campo-editar' data-tipo='{$tipo}' data-campo='{$mmercurio28->getCampo()}'>";
                $response .= "<i class='fas fa-user-edit text-white'></i>";
                $response .= "</a>";
                $response .= "<a href='#!' class='table-action btn btn-xs btn-primary' data-toggle='campo-borrar' data-tipo='{$tipo}' data-campo='{$mmercurio28->getCampo()}'>";
                $response .= "<i class='fas fa-trash text-white'></i>";
                $response .= "</a>";
                $response .= "</td>";
                $response .= "</tr>";
            }
            return $this->renderObject($response, false);
        } catch (DbException $e) {
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function guardarCampoAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $tipo = $this->getPostParam('tipo', "addslaches", "alpha", "extraspaces", "striptags");
                $campo = $this->getPostParam('campo', "addslaches", "alpha", "extraspaces", "striptags");
                $detalle = $this->getPostParam('detalle', "addslaches", "alpha", "extraspaces", "striptags");
                $orden = $this->getPostParam('orden', "addslaches", "extraspaces", "striptags");
                $modelos = array("mercurio28");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $mercurio28 = new Mercurio28();
                $mercurio28->setTransaction($Transaccion);
                $mercurio28->setTipo($tipo);
                $mercurio28->setCampo($campo);
                $mercurio28->setDetalle($detalle);
                $mercurio28->setOrden($orden);
                if (!$mercurio28->save()) {
                    parent::setLogger($mercurio28->getMessages());
                    parent::ErrorTrans();
                }
                parent::finishTrans();
                $response = parent::successFunc("Movimiento Realizado Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                parent::ErrorTrans();
            }
        } catch (TransactionFailed $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function editarCampoAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $tipo = $this->getPostParam('tipo', "addslaches", "alpha", "extraspaces", "striptags");
                $campo = $this->getPostParam('campo', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("mercurio28");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $mercurio28 = $this->Mercurio28->findFirst("tipo='$tipo' and campo = '$campo'");
                if ($mercurio28 == false) $mercurio28 = new Mercurio28();
                return $this->renderObject($mercurio28->getArray(), false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                parent::ErrorTrans();
            }
        } catch (TransactionFailed $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function borrarCampoAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $tipo = $this->getPostParam('tipo', "addslaches", "alpha", "extraspaces", "striptags");
                $campo = $this->getPostParam('campo', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("mercurio28");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $this->Mercurio28->deleteAll("tipo='$tipo' and campo='$campo'");
                parent::finishTrans();
                $response = parent::successFunc("Movimiento Realizado Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                parent::ErrorTrans();
            }
        } catch (TransactionFailed $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function reporteAction($format = 'P')
    {
        $this->setResponse("ajax");
        $_fields = array();
        $_fields["tipo"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["detalle"] = array('header' => "Detalle", 'size' => "31", 'align' => "C");

        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio06", $_fields, $this->query, "Tipos Acceso", $format);
        return $this->renderObject($file, false);
    }
}
