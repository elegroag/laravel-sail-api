<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Mercurio06;
use App\Models\Mercurio28;
use App\Services\Tag;
use App\Services\Utils\GeneralService;

class Mercurio06Controller extends ApplicationController
{

    protected $query = "1=1";
    protected $cantidad_pagina = 10;
    protected $db;
    protected $user;
    protected $tipo;

    public function __construct()
    {
        $this->cantidad_pagina = $this->numpaginate;
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
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
        #$this->buscarAction();
    }

    public function changeCantidadPaginaAction()
    {
        #$this->buscarAction();
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
        #Tag::setDocumentTitle('Motivos Tipos Acceso');
    }


    public function buscarAction(Request $request)
    {
        $this->setResponse("ajax");
        $consultasOldServices = new GeneralService();
        $this->query = $consultasOldServices->converQuery();

        $pagina = ($request->input('pagina')) ? $request->input('pagina') : 1;
        $this->cantidad_pagina = ($request->input("numero")) ? $request->input("numero") : 10;

        $paginate = Tag::paginate($this->Mercurio06->find("$this->query"), $pagina, $this->cantidad_pagina);
        $html = self::showTabla($paginate);

        $consultasOldServices = new GeneralService();
        $html_paginate = $consultasOldServices->showPaginate($paginate, $event = 'toggle');

        $response['consulta'] = $html;
        $response['paginate'] = $html_paginate;
        $this->renderObject($response, false);
    }

    public function editarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipo = $request->input('tipo');
            $mercurio06 = $this->Mercurio06->findFirst("tipo = '$tipo'");
            if ($mercurio06 == false) $mercurio06 = new Mercurio06();

            return $this->renderObject($mercurio06->getArray(), false);
        } catch (DebugException $e) {
            parent::setLogger($e->getMessage());
            $this->db->rollback();
        }
    }

    public function borrarAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $tipo = $request->input('tipo');
            $modelos = array("Mercurio06");

            $response = $this->db->begin();
            $this->Mercurio06->deleteAll("tipo = '$tipo'");
            $this->db->commit();
            $response = parent::successFunc("Borrado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede Borrar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function guardarAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $tipo = $request->input('tipo', "addslaches", "alpha", "extraspaces", "striptags");
            $detalle = $request->input('detalle', "addslaches", "alpha", "extraspaces", "striptags");
            $modelos = array("Mercurio06");

            $response = $this->db->begin();
            $mercurio06 = new Mercurio06();

            $mercurio06->setTipo($tipo);
            $mercurio06->setDetalle($detalle);
            if (!$mercurio06->save()) {
                parent::setLogger($mercurio06->getMessages());
                $this->db->rollback();
            }
            $this->db->commit();
            $response = parent::successFunc("Creacion Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede guardar/editar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function validePkAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipo = $request->input('tipo', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio06->count("*", "conditions: tipo = '$tipo'");
            if ($l > 0) {
                $response = parent::errorFunc("El Registro ya se encuentra Digitado");
            }
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function validePkCampoAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipo = $request->input('tipo', "addslaches", "alpha", "extraspaces", "striptags");
            $campo = $request->input('campo_28', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio28->count("*", "conditions: tipo = '$tipo' and campo='$campo'");
            if ($l > 0) {
                $response = parent::errorFunc("El Registro ya se encuentra Digitado");
            }
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function campo_viewAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipo = $request->input('tipo', "addslaches", "alpha", "extraspaces", "striptags");
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
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function guardarCampoAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $tipo = $request->input('tipo', "addslaches", "alpha", "extraspaces", "striptags");
            $campo = $request->input('campo', "addslaches", "alpha", "extraspaces", "striptags");
            $detalle = $request->input('detalle', "addslaches", "alpha", "extraspaces", "striptags");
            $orden = $request->input('orden', "addslaches", "extraspaces", "striptags");
            $modelos = array("mercurio28");

            $response = $this->db->begin();
            $mercurio28 = new Mercurio28();

            $mercurio28->setTipo($tipo);
            $mercurio28->setCampo($campo);
            $mercurio28->setDetalle($detalle);
            $mercurio28->setOrden($orden);
            if (!$mercurio28->save()) {
                parent::setLogger($mercurio28->getMessages());
                $this->db->rollback();
            }
            $this->db->commit();
            $response = parent::successFunc("Movimiento Realizado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function editarCampoAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $tipo = $request->input('tipo', "addslaches", "alpha", "extraspaces", "striptags");
            $campo = $request->input('campo', "addslaches", "alpha", "extraspaces", "striptags");
            $modelos = array("mercurio28");

            $response = $this->db->begin();
            $mercurio28 = $this->Mercurio28->findFirst("tipo='$tipo' and campo = '$campo'");
            if ($mercurio28 == false) $mercurio28 = new Mercurio28();
            return $this->renderObject($mercurio28->getArray(), false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function borrarCampoAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $tipo = $request->input('tipo', "addslaches", "alpha", "extraspaces", "striptags");
            $campo = $request->input('campo', "addslaches", "alpha", "extraspaces", "striptags");
            $modelos = array("mercurio28");

            $response = $this->db->begin();
            $this->Mercurio28->deleteAll("tipo='$tipo' and campo='$campo'");
            $this->db->commit();
            $response = parent::successFunc("Movimiento Realizado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
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
