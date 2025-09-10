<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio50Controller extends ApplicationController
{

    private $query = "1=1";
    private $cantidad_pagina = 0;

    public function __construct()
    {
       
        
        
        $this->cantidad_pagina = $this->numpaginate;
        
    }

    public function showTabla($paginate)
    {
        $html  = '<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">';
        $html .= "<thead class='thead-light'>";
        $html .= "<tr>";
        $html .= "<th scope='col'>Codigo Aplicativo</th>";
        $html .= "<th scope='col'>Url Webservice</th>";
        $html .= "<th scope='col'>Path</th>";
        $html .= "<th scope='col'>Url Online</th>";
        $html .= "<th scope='col'>Puntos por Compartir</th>";
        $html .= "<th scope='col'>Acciones</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getCodapl()}</td>";
            $html .= "<td>{$mtable->getWebser()}</td>";
            $html .= "<td>{$mtable->getPath()}</td>";
            $html .= "<td>{$mtable->getUrlonl()}</td>";
            $html .= "<td>{$mtable->getPuncom()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' onclick='editar()'>";
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
        $this->cantidad_pagina = $request->input("numero");
        self::buscarAction();
    }

    public function indexAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Basica");
        if ($this->Mercurio50->count() == 0) $this->setParamToView("buttons", array("N"));
        Tag::setDocumentTitle('Basica');
    }


    public function buscarAction()
    {
        $this->setResponse("ajax");
        $pagina = $request->input('pagina');
        if ($pagina == "") $pagina = 1;
        $paginate = Tag::paginate($this->Mercurio50->find("$this->query"), $pagina, $this->cantidad_pagina);
        $html = $this->showTabla($paginate);
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
            $mercurio50 = $this->Mercurio50->findFirst();
            if ($mercurio50 == false) $mercurio50 = new Mercurio50();
            $this->renderObject($mercurio50->getArray(), false);
        } catch (DbException $e) {
            parent::setLogger($e->getMessage());
            $this->db->rollback();
        }
    }

    public function guardarAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $codapl = $request->input('codapl', "addslaches", "extraspaces", "striptags");
                $webser = $request->input('webser', "addslaches", "extraspaces", "striptags");
                $path = $request->input('path', "addslaches", "alpha", "extraspaces", "striptags");
                $urlonl = $request->input('urlonl', "addslaches", "extraspaces", "striptags");
                $puncom = $request->input('puncom', "addslaches", "extraspaces", "striptags");
                $modelos = array("Mercurio50");
                
                $response = $this->db->begin();
                $mercurio50 = new Mercurio50();
                $mercurio50->setTransaction($Transaccion);
                $mercurio50->setCodapl($codapl);
                $mercurio50->setWebser($webser);
                $mercurio50->setPath($path);
                $mercurio50->setUrlonl($urlonl);
                $mercurio50->setPuncom($puncom);
                if (!$mercurio50->save()) {
                    parent::setLogger($mercurio50->getMessages());
                    $this->db->rollback();
                }
                $this->db->commit();
                $response = parent::successFunc("Creacion Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede guardar/editar el Registro");
            return $this->renderObject($response, false);
        }
    }
}
