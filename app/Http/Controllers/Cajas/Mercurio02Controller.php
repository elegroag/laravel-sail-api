<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio02Controller extends ApplicationController
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
        $permisos = array("aplicarFiltro" => "5", "editar" => "6", "guardar" => "7", "buscar" => "8");
        $flag = parent::beforeFilter($permisos);
        if (!$flag) {
            $response = parent::errorFunc("No cuenta con los permisos para este proceso");
            if (is_ajax()) {
                $this->setResponse("ajax");
                $this->renderObject($response, false);
            } else {
                Router::redirectToApplication('Cajas/entrada/index');
            }
            return false; //habilitados los permisos
        }
    }


    public function showTabla($paginate)
    {
        $html  = '<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">';
        $html .= "<thead class='thead-light'>";
        $html .= "<tr>";
        $html .= "<th scope='col'>Caja</th>";
        $html .= "<th scope='col'>Nit</th>";
        $html .= "<th scope='col'>Razon Social</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getCodcaj()}</td>";
            $html .= "<td>{$mtable->getNit()}</td>";
            $html .= "<td>{$mtable->getRazsoc()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' data-cid='{$mtable->getCodcaj()}' data-toggle='editar'>";
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
        $this->setParamToView("title", "Datos Caja");
        Tag::setDocumentTitle('Datos Caja');
        if ((new Mercurio02)->count() == 0) $this->setParamToView("buttons", array("N"));

        $apiRest = Comman::Api();
        $apiRest->runCli(
            array(
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'listar_ciudades_departamentos'
            )
        );

        $data = $apiRest->toArray();
        $data = $data['ciudades'];
        $_codciu = array();
        if (is_array($data)) foreach ($data as $mcodciu) $_codciu[$mcodciu['codciu']] = $mcodciu['detciu'];
        $this->setParamToView("ciudades", $_codciu);
    }

    public function buscarAction()
    {
        $this->setResponse("ajax");
        $pagina = $this->getPostParam('pagina');
        if ($pagina == "") $pagina = 1;
        $paginate = Tag::paginate($this->Mercurio02->find("$this->query"), $pagina, $this->cantidad_pagina);
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
            $mercurio02 = $this->Mercurio02->findFirst();
            if ($mercurio02 == false) $mercurio02 = new Mercurio02();

            return $this->renderObject($mercurio02->getArray(), false);
        } catch (DbException $e) {
            parent::setLogger($e->getMessage());
            parent::ErrorTrans();
        }
    }

    public function guardarAction()
    {
        $this->setResponse("ajax");
        try {
            try {
                $codcaj = $this->getPostParam('codcaj', "addslaches", "extraspaces", "striptags");
                $nit = $this->getPostParam('nit', "addslaches", "extraspaces", "striptags");
                $razsoc = $this->getPostParam('razsoc', "addslaches", "alpha", "extraspaces", "striptags");
                $sigla = $this->getPostParam('sigla', "addslaches", "extraspaces", "striptags");
                $email = $this->getPostParam('email', "addslaches", "extraspaces", "striptags");
                $direccion = $this->getPostParam('direccion', "addslaches", "extraspaces", "striptags");
                $telefono = $this->getPostParam('telefono', "addslaches", "alpha", "extraspaces", "striptags");
                $codciu = $this->getPostParam('codciu', "addslaches", "extraspaces", "striptags");
                $pagweb = $this->getPostParam('pagweb', "addslaches", "extraspaces", "striptags");
                $pagfac = $this->getPostParam('pagfac', "addslaches", "extraspaces", "striptags");
                $pagtwi = $this->getPostParam('pagtwi', "addslaches", "extraspaces", "striptags");
                $pagyou = $this->getPostParam('pagyou', "addslaches", "extraspaces", "striptags");
                $modelos = array("Mercurio02");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $mercurio02 = new Mercurio02();
                $mercurio02->setTransaction($Transaccion);
                $mercurio02->setCodcaj($codcaj);
                $mercurio02->setNit($nit);
                $mercurio02->setRazsoc($razsoc);
                $mercurio02->setSigla($sigla);
                $mercurio02->setEmail($email);
                $mercurio02->setDireccion($direccion);
                $mercurio02->setTelefono($telefono);
                $mercurio02->setCodciu($codciu);
                $mercurio02->setPagweb($pagweb);
                $mercurio02->setPagfac($pagfac);
                $mercurio02->setPagtwi($pagtwi);
                $mercurio02->setPagyou($pagyou);
                if (!$mercurio02->save()) {
                    throw new Exception("Error " . $mercurio02->getMessages(), 501);
                }

                parent::finishTrans();
                $response = parent::successFunc("Creacion Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::ErrorTrans($e->getMessage());
            } catch (Exception $err) {
                parent::ErrorTrans($err->getMessage());
            }
        } catch (TransactionFailed $error) {
            $response = parent::errorFunc("No se puede guardar/editar el registro " . $error->getMessage());
        }
        return $this->renderObject($response, false);
    }
}
