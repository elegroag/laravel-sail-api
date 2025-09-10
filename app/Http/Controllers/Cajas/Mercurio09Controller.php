<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio09;
use App\Models\Mercurio13;
use App\Models\Mercurio14;
use App\Services\Tag;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio09Controller extends ApplicationController
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
            "tipopc" => "Codigo",
            "detalle" => "Detalle",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Tipos Opciones");
        $this->setParamToView("buttons", array("N", "F", "R"));
        # Tag::setDocumentTitle('Motivos Tipos Opciones');

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

    public function buscarAction(Request $request)
    {
        $this->setResponse("ajax");
        $pagina = $request->input('pagina');
        if ($pagina == "") $pagina = 1;
        $paginate = Tag::paginate($this->Mercurio09->find("$this->query"), $pagina, $this->cantidad_pagina);
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
            $tipopc = $request->input('tipopc');
            $mercurio09 = $this->Mercurio09->findFirst("tipopc = '$tipopc'");
            if ($mercurio09 == false) $mercurio09 = new Mercurio09();
            $this->renderObject($mercurio09->getArray(), false);
        } catch (DebugException $e) {

            $this->db->rollback();
        }
    }

    public function borrarAction(Request $request)
    {
        try {
            try {
                $this->setResponse("ajax");
                $tipopc = $request->input('tipopc');
                $modelos = array("Mercurio09");

                $response = $this->db->begin();
                $this->Mercurio09->deleteAll("tipopc = '$tipopc'");
                $this->db->commit();
                $response = parent::successFunc("Borrado Con Exito");
                return $this->renderObject($response, false);
            } catch (DebugException $e) {
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede Borrar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function guardarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
            $detalle = $request->input('detalle', "addslaches", "alpha", "extraspaces", "striptags");
            $dias = $request->input('dias', "addslaches", "alpha", "extraspaces", "striptags");
            $modelos = array("Mercurio09");

            $response = $this->db->begin();
            $mercurio09 = new Mercurio09();

            $mercurio09->setTipopc($tipopc);
            $mercurio09->setDetalle($detalle);
            $mercurio09->setDias($dias);
            if (!$mercurio09->save()) {
                parent::setLogger($mercurio09->getMessages());
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
            $tipopc = $request->input('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio09->count("*", "conditions: tipopc = '$tipopc'");
            if ($l > 0) {
                $response = parent::errorFunc("El Registro ya se encuentra Digitado");
            }
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function archivos_viewAction(Request $request)
    {
        try {
            $this->setResponse("view");
            $tipopc = $request->input('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
            $response = "";
            $mercurio12 = $this->Mercurio12->find();
            return view('mercurio09/tmp/archivos_view', array(
                'tipopc' => $tipopc,
                'mercurio12' => $mercurio12,
                "Mercurio13" => new Mercurio13()
            ));
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderText($response);
        }
    }

    public function guardarArchivosAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
            $coddoc = $request->input('coddoc', "addslaches", "alpha", "extraspaces", "striptags");
            $acc = $request->input('acc', "addslaches", "extraspaces", "striptags");
            $item = 1;
            $modelos = array("mercurio13");

            $response = $this->db->begin();
            if ($acc == "1") {
                $mercurio13 = new Mercurio13();

                $mercurio13->setTipopc($tipopc);
                $mercurio13->setCoddoc($coddoc);
                $mercurio13->setObliga("N");
                if (!$mercurio13->save()) {
                    parent::setLogger($mercurio13->getMessages());
                    $this->db->rollback();
                }
            } else {
                $this->Mercurio13->deleteAll("tipopc='$tipopc' and coddoc='$coddoc' ");
            }
            $this->db->commit();
            $response = parent::successFunc("Movimiento Realizado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function obligaArchivosAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc');
            $coddoc = $request->input('coddoc');
            $obliga = $request->input('obliga');

            $response = $this->db->begin();
            $this->Mercurio13->updateAll("obliga='$obliga'", "conditions: tipopc='$tipopc' and coddoc='$coddoc' ");
            $this->db->commit();
            $response = parent::successFunc("Movimiento Realizado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function archivos_empresa_viewAction(Request $request)
    {
        $this->setResponse("view");
        try {
            $tipopc = $request->input('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
            $tipsoc = $request->input('tipsoc', "addslaches", "alpha", "extraspaces", "striptags");
            $response = "";
            $mercurio12 = $this->Mercurio12->find();
            return view('mercurio09/tmp/archivos_empresas', array(
                'tipopc' => $tipopc,
                'tipsoc' => $tipsoc,
                'mercurio12' => $mercurio12,
                "Mercurio14" => new Mercurio14()
            ));
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderText($response);
        }
    }

    public function guardarEmpresaArchivosAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc');
            $tipsoc = $request->input('tipsoc');
            $coddoc = $request->input('coddoc');
            $acc = $request->input('acc');

            $response = $this->db->begin();
            if ($acc == "1") {
                $mercurio14 = new Mercurio14();

                $mercurio14->setTipopc($tipopc);
                $mercurio14->setTipsoc($tipsoc);
                $mercurio14->setCoddoc($coddoc);
                $mercurio14->setObliga("N");
                if (!$mercurio14->save()) {
                    parent::setLogger($mercurio14->getMessages());
                    $this->db->rollback();
                }
            } else {
                $this->Mercurio14->deleteAll("tipopc='$tipopc' and tipsoc='$tipsoc' and coddoc='$coddoc' ");
            }
            $this->db->commit();
            $response = parent::successFunc("Movimiento Realizado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function obligaEmpresaArchivosAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc');
            $tipsoc = $request->input('tipsoc');
            $coddoc = $request->input('coddoc');
            $obliga = $request->input('obliga');

            $response = $this->db->begin();
            $this->Mercurio14->updateAll("obliga='$obliga'", "conditions: tipopc='$tipopc' and tipsoc='$tipsoc' and coddoc='$coddoc' ");
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
        $_fields["tipopc"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["detalle"] = array('header' => "Detalle", 'size' => "31", 'align' => "C");
        $_fields["dias"] = array('header' => "Dias", 'size' => "31", 'align' => "C");
        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio09", $_fields, $this->query, "Tipos Opciones", $format);
        return $this->renderObject($file, false);
    }
}
