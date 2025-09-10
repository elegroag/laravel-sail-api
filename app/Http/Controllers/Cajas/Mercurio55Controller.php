<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio55;
use App\Services\Tag;
use App\Services\Utils\GeneralService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio55Controller extends ApplicationController
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
        $html .= "<th scope='col'>Codigo</th>";
        $html .= "<th scope='col'>Detalle</th>";
        $html .= "<th scope='col'>Categoria</th>";
        $html .= "<th scope='col'>Tipo</th>";
        $html .= "<th scope='col'>Estado</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getCodare()}</td>";
            $html .= "<td>{$mtable->getDetalle()}</td>";
            $html .= "<td>{$mtable->getTipoDetalle()}</td>";
            $html .= "<td>{$mtable->getCodcatDetalle()}</td>";
            $html .= "<td>{$mtable->getEstadoDetalle()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Archivos' onclick='irArchivos(\"{$mtable->getCodare()}\")'>";
            $html .= "<i class='fas fa-images'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' onclick='editar(\"{$mtable->getCodare()}\")'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' onclick='borrar(\"{$mtable->getCodare()}\")'>";
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
        #self::buscarAction();
    }

    public function changeCantidadPaginaAction(Request $request)
    {
        $this->setResponse("ajax");
        $this->cantidad_pagina = $request->input("numero");
        #self::buscarAction();
    }

    public function nuevoAction()
    {
        $this->setResponse("ajax");
        $numero = $this->Mercurio55->maximum("codare") + 1;
        $response = parent::successFunc("ok", $numero);
        $this->renderObject($response, false);
    }

    public function indexAction()
    {
        $campo_field = array(
            "codare" => "Codigo",
            "detalle" => "Detalle",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Areas");
        $this->setParamToView("buttons", array("N", "F", "R"));
        #Tag::setDocumentTitle('Areas');
    }


    public function buscarAction(Request $request)
    {
        $this->setResponse("ajax");
        $pagina = $request->input('pagina');
        if ($pagina == "") $pagina = 1;
        $paginate = Tag::paginate($this->Mercurio55->find("$this->query"), $pagina, $this->cantidad_pagina);
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
            $codare = $request->input('codare');
            $mercurio55 = $this->Mercurio55->findFirst("codare = '$codare'");
            if ($mercurio55 == false) $mercurio55 = new Mercurio55();
            return $this->renderObject($mercurio55->getArray(), false);
        } catch (DebugException $e) {
            parent::setLogger($e->getMessage());
            $this->db->rollback();
        }
    }

    public function borrarAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $codare = $request->input('codare');
            $modelos = array("Mercurio55");

            $response = $this->db->begin();
            $this->Mercurio55->deleteAll("codare = '$codare'");
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
            $codare = $request->input('codare');
            $detalle = $request->input('detalle');
            $codcat = $request->input('codcat');
            $tipo = $request->input('tipo');
            $estado = $request->input('estado');

            $response = $this->db->begin();
            $mercurio55 = new Mercurio55();

            $mercurio55->setCodare($codare);
            $mercurio55->setDetalle($detalle);
            $mercurio55->setCodcat($codcat);
            $mercurio55->setTipo($tipo);
            $mercurio55->setEstado($estado);
            if (!$mercurio55->save()) {
                parent::setLogger($mercurio55->getMessages());
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
            $codare = $request->input('codare', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio55->count("*", "conditions: codare = '$codare'");
            if ($l > 0) {
                $response = parent::errorFunc("El Registro ya se encuentra Digitado");
            }
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function reporteAction($format = 'P')
    {
        $this->setResponse("ajax");
        $_fields = array();
        $_fields["codare"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["detalle"] = array('header' => "Detalle", 'size' => "31", 'align' => "C");
        $_fields["tipo"] = array('header' => "Tipo", 'size' => "31", 'align' => "C");
        $_fields["codcat"] = array('header' => "Categoria", 'size' => "31", 'align' => "C");
        $_fields["estado"] = array('header' => "Estado", 'size' => "31", 'align' => "C");
        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio55", $_fields, $this->query, "Categorias", $format);
        return $this->renderObject($file, false);
    }
}
