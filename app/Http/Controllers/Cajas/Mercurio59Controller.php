<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio59Controller extends ApplicationController
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
        $html .= "<th scope='col'>Codigo</th>";
        $html .= "<th scope='col'>Nota</th>";
        $html .= "<th scope='col'>Email</th>";
        $html .= "<th scope='col'>Pregunta cantidad</th>";
        $html .= "<th scope='col'>Automatico Servicio</th>";
        $html .= "<th scope='col'>Consumo</th>";
        $html .= "<th scope='col'>Estado</th>";
        $html .= "<th scope='col'>Imagen</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getCodser()}</td>";
            $html .= "<td>{$mtable->getNota()}</td>";
            $html .= "<td>{$mtable->getEmail()}</td>";
            $html .= "<td>{$mtable->getPrecanDetalle()}</td>";
            $html .= "<td>{$mtable->getAutserDetalle()}</td>";
            $html .= "<td>{$mtable->getConsumo()}</td>";
            $html .= "<td>{$mtable->getEstadoDetalle()}</td>";
            $html .= "<td>{$mtable->getArchivo()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' onclick='editar(\"{$mtable->getCodinf()}\",\"{$mtable->getCodser()}\",\"{$mtable->getNumero()}\")'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' onclick='borrar(\"{$mtable->getCodinf()}\",\"{$mtable->getCodser()}\",\"{$mtable->getNumero()}\")'>";
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
        self::buscarAction();
    }

    public function changeCantidadPaginaAction()
    {
        $this->setResponse("ajax");
        $this->cantidad_pagina = $request->input("numero");
        self::buscarAction();
    }

    public function indexAction($codinf = "")
    {
        //Debug::addVariable("prueba",$_GET["codinf"]);
        //throw new DebugException(0);
        $campo_field = array(
            "codser" => "Servicio",
            "email" => "Email",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Servicios");
        $this->setParamToView("codinf", $_GET["codinf"]);
        $this->setParamToView("buttons", array("N", "F", "R"));
        Tag::setDocumentTitle('Servicios');
        $consultasOldServices = new GeneralService();
        $codser = $consultasOldServices->webService("servicios", array());
        $_codser = array();
        foreach ($codser['data'] as $data) $_codser[$data['codser']] = $data['detalle'];
        $this->setParamToView("_codser", $_codser);
    }

    public function traerAperturaAction()
    {
        $this->setResponse("ajax");
        $codser = $request->input("codser");
        $consultasOldServices = new GeneralService();
        $servi29 = $consultasOldServices->webService("aperturas_servicios", array("codser" => $codser));
        $_servi29 = array();
        foreach ($servi29['data'] as $data) $_servi29[$data['numero']] = $data['detalle'];
        $response = Tag::selectStatic("numero", $_servi29, "use_dummy: true", "dummyValue:", "class: form-control");
        $this->renderObject($response, false);
    }


    public function buscarAction()
    {
        $this->setResponse("ajax");
        $pagina = $request->input('pagina');
        if ($pagina == "") $pagina = 1;
        $paginate = Tag::paginate($this->Mercurio59->find("$this->query"), $pagina, $this->cantidad_pagina);
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
            $codinf = $request->input('codinf');
            $codser = $request->input('codser');
            $numero = $request->input('numero');
            $mercurio59 = $this->Mercurio59->findFirst("codinf = '$codinf' and codser='$codser' and numero='$numero'");
            if ($mercurio59 == false) $mercurio59 = new Mercurio59();
            $this->renderObject($mercurio59->getArray(), false);
        } catch (DbException $e) {
            parent::setLogger($e->getMessage());
            $this->db->rollback();
        }
    }

    public function borrarAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $codinf = $request->input('codinf');
                $codser = $request->input('codser');
                $numero = $request->input('numero');
                $modelos = array("Mercurio59");
                
                $response = $this->db->begin();
                $this->Mercurio59->deleteAll("codinf = '$codinf' and codser='$codser' and numero='$numero'");
                $this->db->commit();
                $response = parent::successFunc("Borrado Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede Borrar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function guardarAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $codinf = $request->input('codinf', "addslaches", "extraspaces", "striptags");
                $codser = $request->input('codser', "addslaches", "alpha", "extraspaces", "striptags");
                $numero = $request->input('numero', "addslaches", "alpha", "extraspaces", "striptags");
                $nota = $request->input('nota', "addslaches", "extraspaces", "striptags");
                $email = $request->input('email', "addslaches", "extraspaces", "striptags");
                $precan = $request->input('precan', "addslaches", "alpha", "extraspaces", "striptags");
                $autser = $request->input('autser', "addslaches", "alpha", "extraspaces", "striptags");
                $consumo = $request->input('consumo', "addslaches", "alpha", "extraspaces", "striptags");
                $estado = $request->input('estado', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("Mercurio59");
                
                $response = $this->db->begin();
                $mercurio59 = new Mercurio59();
                $mercurio59->setTransaction($Transaccion);
                $mercurio59->setCodinf($codinf);
                $mercurio59->setCodser($codser);
                $mercurio59->setNumero($numero);
                $mercurio59->setNota($nota);
                $mercurio59->setEmail($email);
                $mercurio59->setPrecan($precan);
                $mercurio59->setAutser($autser);
                $mercurio59->setConsumo($consumo);
                $mercurio59->setEstado($estado);
                $mercurio01 = $this->Mercurio01->findFirst();
                if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != "") {
                    $extension = explode(".", $_FILES['archivo']['name']);
                    $name = $codinf . $codser . "_infraservi." . end($extension);
                    $_FILES['archivo']['name'] = $name;
                    $estado = $this->uploadFile("archivo", $mercurio01->getPath() . "/galeria");
                    if ($estado != false) {
                        $mercurio59->setArchivo($name);
                        if (!$mercurio59->save()) {
                            parent::setLogger($mercurio59->getMessages());
                            $this->db->rollback();
                        }
                        $response = parent::successFunc("Se adjunto con exito el archivo");
                    } else {
                        $response = parent::errorFunc("No se cargo: Tamano del archivo muy grande o No es Valido");
                    }
                } else {
                    $response = parent::errorFunc("No se cargo el archivo");
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

    public function validePkAction()
    {
        try {
            $this->setResponse("ajax");
            $codinf = $request->input('codinf', "addslaches", "alpha", "extraspaces", "striptags");
            $codser = $request->input('codser', "addslaches", "alpha", "extraspaces", "striptags");
            $numero = $request->input('numero', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio59->count("*", "conditions: codinf = '$codinf' and codser='$codser' and numero='$numero' ");
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
        $_fields["codinf"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["codser"] = array('header' => "Servicio", 'size' => "15", 'align' => "C");
        $_fields["numero"] = array('header' => "Numero", 'size' => "15", 'align' => "C");
        $_fields["nota"] = array('header' => "Nota", 'size' => "31", 'align' => "C");
        $_fields["email"] = array('header' => "Email", 'size' => "31", 'align' => "C");
        $_fields["precan"] = array('header' => "Presenta Cantidad", 'size' => "31", 'align' => "C");
        $_fields["autser"] = array('header' => "Automatico Servicio", 'size' => "31", 'align' => "C");
        $_fields["estado"] = array('header' => "Estado", 'size' => "31", 'align' => "C");
        $_fields["archivo"] = array('header' => "Archivo", 'size' => "31", 'align' => "C");
        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio59", $_fields, $this->query, "Servicios", $format);
        return $this->renderObject($file, false);
    }
}
