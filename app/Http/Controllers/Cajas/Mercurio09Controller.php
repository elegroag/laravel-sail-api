<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio09Controller extends ApplicationController
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

    public function beforeFilter($permisos = array())
    {
        $permisos = array("aplicarFiltro" => "27", "editar" => "28", "guardar" => "29", "buscar" => "30", "borrar" => "31");
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
        $html .= "<th scope='col'>Tipopc</th>";
        $html .= "<th scope='col'>Detalle</th>";
        $html .= "<th scope='col'>Dias</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getTipopc()}</td>";
            $html .= "<td>{$mtable->getDetalle()}</td>";
            $html .= "<td>{$mtable->getDias()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a type='button' class='table-action btn btn-xs btn-warning' title='Documento' data-cid='{$mtable->getTipopc()}' data-toggle='archivos-view'>";
            $html .= "<i class='fas fa-file-image text-white'></i>";
            $html .= "</a>";
            if (!in_array($mtable->getTipopc(), array("1", "3", "4", "7"))) {
                $html .= "<a type='button' class='table-action btn btn-xs btn-success' title='Documento' data-cid='{$mtable->getTipopc()}' data-toggle='empresa-view'>";
                $html .= "<i class='fas fa-eye text-white'></i>";
                $html .= "</a>";
            }
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' data-cid='{$mtable->getTipopc()}' data-toggle='editar'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
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
            "tipopc" => "Codigo",
            "detalle" => "Detalle",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Tipos Opciones");
        $this->setParamToView("buttons", array("N", "F", "R"));
        Tag::setDocumentTitle('Motivos Tipos Opciones');

        $apiRest = Comman::Api();
        $apiRest->runCli(
            array(
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_empresa',
            )
        );
        $datos_captura = $apiRest->toArray();
        $_tipsoc = array();
        foreach ($datos_captura['tipo_sociedades'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        $this->setParamToView("_tipsoc", $_tipsoc);
    }

    public function buscarAction()
    {
        $this->setResponse("ajax");
        $pagina = $this->getPostParam('pagina');
        if ($pagina == "") $pagina = 1;
        $paginate = Tag::paginate($this->Mercurio09->find("$this->query"), $pagina, $this->cantidad_pagina);
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
            $tipopc = $this->getPostParam('tipopc');
            $mercurio09 = $this->Mercurio09->findFirst("tipopc = '$tipopc'");
            if ($mercurio09 == false) $mercurio09 = new Mercurio09();
            $this->renderObject($mercurio09->getArray(), false);
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
                $tipopc = $this->getPostParam('tipopc');
                $modelos = array("Mercurio09");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $this->Mercurio09->deleteAll("tipopc = '$tipopc'");
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
                $tipopc = $this->getPostParam('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
                $detalle = $this->getPostParam('detalle', "addslaches", "alpha", "extraspaces", "striptags");
                $dias = $this->getPostParam('dias', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("Mercurio09");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $mercurio09 = new Mercurio09();
                $mercurio09->setTransaction($Transaccion);
                $mercurio09->setTipopc($tipopc);
                $mercurio09->setDetalle($detalle);
                $mercurio09->setDias($dias);
                if (!$mercurio09->save()) {
                    parent::setLogger($mercurio09->getMessages());
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
            $tipopc = $this->getPostParam('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio09->count("*", "conditions: tipopc = '$tipopc'");
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

    public function archivos_viewAction()
    {
        try {
            $this->setResponse("view");
            $tipopc = $this->getPostParam('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
            $response = "";
            $mercurio12 = $this->Mercurio12->find();
            View::renderView('mercurio09/tmp/archivos_view', array(
                'tipopc' => $tipopc,
                'mercurio12' => $mercurio12,
                "Mercurio13" => new Mercurio13()
            ));
        } catch (DbException $e) {
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderText($response);
        }
    }

    public function guardarArchivosAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $tipopc = $this->getPostParam('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
                $coddoc = $this->getPostParam('coddoc', "addslaches", "alpha", "extraspaces", "striptags");
                $acc = $this->getPostParam('acc', "addslaches", "extraspaces", "striptags");
                $item = 1;
                $modelos = array("mercurio13");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                if ($acc == "1") {
                    $mercurio13 = new Mercurio13();
                    $mercurio13->setTransaction($Transaccion);
                    $mercurio13->setTipopc($tipopc);
                    $mercurio13->setCoddoc($coddoc);
                    $mercurio13->setObliga("N");
                    if (!$mercurio13->save()) {
                        parent::setLogger($mercurio13->getMessages());
                        parent::ErrorTrans();
                    }
                } else {
                    $this->Mercurio13->deleteAll("tipopc='$tipopc' and coddoc='$coddoc' ");
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

    public function obligaArchivosAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $tipopc = $this->getPostParam('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
                $coddoc = $this->getPostParam('coddoc', "addslaches", "alpha", "extraspaces", "striptags");
                $obliga = $this->getPostParam('obliga', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("mercurio13");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $this->Mercurio13->updateAll("obliga='$obliga'", "conditions: tipopc='$tipopc' and coddoc='$coddoc' ");
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

    public function archivos_empresa_viewAction()
    {
        $this->setResponse("view");
        try {
            $tipopc = $this->getPostParam('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
            $tipsoc = $this->getPostParam('tipsoc', "addslaches", "alpha", "extraspaces", "striptags");
            $response = "";
            $mercurio12 = $this->Mercurio12->find();
            View::renderView('mercurio09/tmp/archivos_empresas', array(
                'tipopc' => $tipopc,
                'tipsoc' => $tipsoc,
                'mercurio12' => $mercurio12,
                "Mercurio14" => new Mercurio14()
            ));
        } catch (DbException $e) {
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderText($response);
        }
    }

    public function guardarEmpresaArchivosAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $tipopc = $this->getPostParam('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
                $tipsoc = $this->getPostParam('tipsoc', "addslaches", "alpha", "extraspaces", "striptags");
                $coddoc = $this->getPostParam('coddoc', "addslaches", "alpha", "extraspaces", "striptags");
                $acc = $this->getPostParam('acc', "addslaches", "extraspaces", "striptags");
                $item = 1;
                $modelos = array("mercurio14");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                if ($acc == "1") {
                    $mercurio14 = new Mercurio14();
                    $mercurio14->setTransaction($Transaccion);
                    $mercurio14->setTipopc($tipopc);
                    $mercurio14->setTipsoc($tipsoc);
                    $mercurio14->setCoddoc($coddoc);
                    $mercurio14->setObliga("N");
                    if (!$mercurio14->save()) {
                        parent::setLogger($mercurio14->getMessages());
                        parent::ErrorTrans();
                    }
                } else {
                    $this->Mercurio14->deleteAll("tipopc='$tipopc' and tipsoc='$tipsoc' and coddoc='$coddoc' ");
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

    public function obligaEmpresaArchivosAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $tipopc = $this->getPostParam('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
                $tipsoc = $this->getPostParam('tipsoc', "addslaches", "alpha", "extraspaces", "striptags");
                $coddoc = $this->getPostParam('coddoc', "addslaches", "alpha", "extraspaces", "striptags");
                $obliga = $this->getPostParam('obliga', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("mercurio13");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $this->Mercurio14->updateAll("obliga='$obliga'", "conditions: tipopc='$tipopc' and tipsoc='$tipsoc' and coddoc='$coddoc' ");
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
        $_fields["tipopc"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["detalle"] = array('header' => "Detalle", 'size' => "31", 'align' => "C");
        $_fields["dias"] = array('header' => "Dias", 'size' => "31", 'align' => "C");
        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio09", $_fields, $this->query, "Tipos Opciones", $format);
        return $this->renderObject($file, false);
    }
}
