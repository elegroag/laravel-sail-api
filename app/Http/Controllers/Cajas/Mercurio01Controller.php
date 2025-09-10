<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio01Controller extends ApplicationController
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
        $permisos = array("aplicarFiltro" => "1", "editar" => "2", "guardar" => "3", "buscar" => "4");
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
        $html .= "<th scope='col'>Aplicativo</th>";
        $html .= "<th scope='col'>Email</th>";
        $html .= "<th scope='col'>Path</th>";
        $html .= "<th scope='col'>Server</th>";
        $html .= "<th scope='col'>Option</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getCodapl()}</td>";
            $html .= "<td>{$mtable->getEmail()}</td>";
            $html .= "<td>{$mtable->getPath()}</td>";
            $html .= "<td>{$mtable->getFtpserver()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='editar' data-cid='{$mtable->getCodapl()}' data-toggle='editar'>";
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
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Basica");
        if ($this->Mercurio01->count() == 0) $this->setParamToView("buttons", array("N"));
        Tag::setDocumentTitle('Basica');
    }


    public function buscarAction()
    {
        $this->setResponse("ajax");
        $pagina = $this->getPostParam('pagina');
        if ($pagina == "") $pagina = 1;
        $paginate = Tag::paginate($this->Mercurio01->find("$this->query"), $pagina, $this->cantidad_pagina);
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
            $mercurio01 = $this->Mercurio01->findFirst();
            if ($mercurio01 == false) $mercurio01 = new Mercurio01();

            return $this->renderObject($mercurio01->getArray(), false);
        } catch (DbException $e) {
            parent::setLogger($e->getMessage());
            parent::ErrorTrans();
        }
    }

    public function guardarAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $codapl = $this->getPostParam('codapl', "addslaches", "extraspaces", "striptags");
                $email = $this->getPostParam('email', "addslaches", "extraspaces", "striptags");
                $clave = $this->getPostParam('clave', "addslaches", "alpha", "extraspaces", "striptags");
                $path = $this->getPostParam('path', "addslaches", "extraspaces", "striptags");
                $ftpserver = $this->getPostParam('ftpserver', "addslaches", "extraspaces", "striptags");
                $pathserver = $this->getPostParam('pathserver', "addslaches", "extraspaces", "striptags");
                $userserver = $this->getPostParam('userserver', "addslaches", "extraspaces", "striptags");
                $passserver = $this->getPostParam('passserver', "addslaches", "extraspaces", "striptags");
                $modelos = array("Mercurio01");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $mercurio01 = new Mercurio01();
                $mercurio01->setTransaction($Transaccion);
                $mercurio01->setCodapl($codapl);
                $mercurio01->setEmail($email);
                $mercurio01->setClave($clave);
                $mercurio01->setPath($path);
                $mercurio01->setFtpserver($ftpserver);
                $mercurio01->setPathserver($pathserver);
                $mercurio01->setUserserver($userserver);
                $mercurio01->setPassserver($passserver);
                if (!$mercurio01->save()) {
                    parent::setLogger($mercurio01->getMessages());
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
}
