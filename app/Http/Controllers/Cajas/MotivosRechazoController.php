<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Services\Tag;
use App\Services\Utils\GeneralService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MotivosrechazoController extends ApplicationController
{

    protected $query = "1=1";
    protected $cantidad_pagina = 0;
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
        $html .= "<th scope='col'>Codest</th>";
        $html .= "<th scope='col'>Detalle</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        // foreach($paginate->items as $mtable){
        $html .= "<tr>";
        /*   $html .= "<td>{$mtable->getCodest()}</td>";
            $html .= "<td>{$mtable->getDetalle()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' onclick='editar(\"{$mtable->getCodest()}\")'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' onclick='borrar(\"{$mtable->getCodest()}\")'>";
            $html .= "<i class='fas fa-trash text-white'></i>";
            $html .= "</a>";
           */
        $html .= "</td>";
        $html .= "</tr>";
        // }
        $html .= "</tbody>";
        $html .= "</table>";
        return $html;
    }

    public function aplicarFiltroAction()
    {
        $this->setResponse("ajax");
        $consultasOldServices = new GeneralService();
        $this->query = $consultasOldServices->converQuery();
        #self::buscarAction();
    }

    public function changeCantidadPaginaAction(Request $request)
    {
        $this->setResponse("ajax");
        $this->cantidad_pagina = $request->input("numero");
        #self::buscarAction();
    }
    public function indexAction()
    {
        $campo_field = array(
            "codest" => "Codest",
            "detalle" => "Detalle",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Motivos Rechazo");
        $this->setParamToView("buttons", array("N", "F", "R"));
        #Tag::setDocumentTitle('Motivos Rechazo');
    }


    public function buscarAction(Request $request)
    {
        $this->setResponse("ajax");
        $pagina = $request->input('pagina');
        $params = $_POST;
        if ($this->query != "1=1") {
            $query = array("query" => $this->query);
            $params = array_merge($query, $_POST);
        }
        if ($pagina == "") $pagina = 1;
        $user = session()->get("user");

        $consultasOldServices = new GeneralService();
        //$sat12 = $this->Sat12->findAllBySql("select sat12.numtraccf,tipdocemp,numdocemp from sat12, sat20 where sat12.numtraccf=sat20.numtraccf and sat20.estado in ('I','CA')");
        $sat28 = $consultasOldServices->webService("datosSat28", $params);
        $sat28 = $sat28["data"];
        if (!empty($sat28)) {
            $sat28 = $consultasOldServices->webService("datosSat28", $params);
            $sat28 = $sat28["data"]["info"];
        }
        $paginate = Tag::paginate($sat28, $pagina, $this->cantidad_pagina);
        $html = self::showTabla($paginate);
        $consultasOldServices = new GeneralService();
        $html_paginate = $consultasOldServices->showPaginate($paginate);
        $response['consulta'] = $html;
        $response['paginate'] = $html_paginate;
        $this->renderObject($response, false);
    }

    public function editarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $codest = $request->input('codest');
            $sat28 = $this->Sat28->findFirst("codest = '$codest'");
            if ($sat28 == false) $sat28 = new Sat28();
            return $this->renderObject($sat28->getArray(), false);
        } catch (DebugException $e) {
            parent::setLogger($e->getMessage());
            $this->db->rollback();
        }
    }

    public function borrarAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $codest = $request->input('codest');
            $modelos = array("Sat28");

            $response = $this->db->begin();
            $this->Sat28->deleteAll("codest = '$codest'");
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
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");
            $detalle = $request->input('detalle', "addslaches", "alpha", "extraspaces", "striptags");
            $modelos = array("Sat28");

            $response = $this->db->begin();
            $sat28 = new Sat28();

            $sat28->setCodest($codest);
            $sat28->setDetalle($detalle);
            if (!$sat28->save()) {
                parent::setLogger($sat28->getMessages());
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
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Sat28->count("*", "conditions: codest = '$codest'");
            if ($l > 0) {
                $response = parent::errorFunc("El Registro ya se encuentra Digitado");
            }
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function reporteAction($format = 'P')
    {
        $this->setResponse("ajax");
        $_fields = array();
        $_fields["codest"] = array('header' => "Codest", 'size' => "15", 'align' => "C");
        $_fields["detalle"] = array('header' => "Detalle", 'size' => "31", 'align' => "C");
        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("sat28", $_fields, $this->query, "Preguntas Seguridad", $format);
        return $this->renderObject($file, false);
    }
}
