<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio56Controller extends ApplicationController
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
        $html .= "<th scope='col'>Email</th>";
        $html .= "<th scope='col'>Telefono</th>";
        $html .= "<th scope='col'>Nota</th>";
        $html .= "<th scope='col'>Estado</th>";
        $html .= "<th scope='col'>Archivo</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getCodinf()}</td>";
            $html .= "<td>{$mtable->getEmail()}</td>";
            $html .= "<td>{$mtable->getTelefono()}</td>";
            $html .= "<td>{$mtable->getNota()}</td>";
            $html .= "<td>{$mtable->getEstadoDetalle()}</td>";
            $html .= "<td>{$mtable->getArchivo()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Servicios' onclick='ir_servicios(\"{$mtable->getCodinf()}\")'>";
            $html .= "<i class='fas fa-clipboard-list text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' onclick='editar(\"{$mtable->getCodinf()}\")'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' onclick='borrar(\"{$mtable->getCodinf()}\")'>";
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

    public function indexAction()
    {
        $campo_field = array(
            "codinf" => "Codigo",
            "estado" => "Estado",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Infraestructura");
        $this->setParamToView("buttons", array("N", "F", "R"));
        Tag::setDocumentTitle('Infraestructura');
        $consultasOldServices = new GeneralService();
        $infraestructura = $consultasOldServices->webService("infraestructuras", array());
        $_infraestructura = array();
        foreach ($infraestructura['data'] as $data) $_infraestructura[$data['codinf']] = $data['nomcom'];
        $this->setParamToView("_infraestructura", $_infraestructura);
    }


    public function buscarAction()
    {
        $this->setResponse("ajax");
        $pagina = $request->input('pagina');
        if ($pagina == "") $pagina = 1;
        $paginate = Tag::paginate($this->Mercurio56->find("$this->query"), $pagina, $this->cantidad_pagina);
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
            $mercurio56 = $this->Mercurio56->findFirst("codinf = '$codinf'");
            if ($mercurio56 == false) $mercurio56 = new Mercurio56();
            $this->renderObject($mercurio56->getArray(), false);
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
                $modelos = array("Mercurio56");
                
                $response = $this->db->begin();
                $this->Mercurio56->deleteAll("codinf = '$codinf'");
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
                $email = $request->input('email', "addslaches", "extraspaces", "striptags");
                $telefono = $request->input('telefono', "addslaches", "extraspaces", "striptags");
                $nota = $request->input('nota', "addslaches", "alpha", "extraspaces", "striptags");
                $estado = $request->input('estado', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("Mercurio56");
                
                $response = $this->db->begin();
                $mercurio56 = new Mercurio56();
                $mercurio56->setTransaction($Transaccion);
                $mercurio56->setCodinf($codinf);
                $mercurio56->setEmail($email);
                $mercurio56->setTelefono($telefono);
                $mercurio56->setNota($nota);
                $mercurio56->setEstado($estado);
                $mercurio01 = $this->Mercurio01->findFirst();
                if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != "") {
                    $extension = explode(".", $_FILES['archivo']['name']);
                    $name = $codinf . "_infracestructura." . end($extension);
                    $_FILES['archivo']['name'] = $name;
                    $estado = $this->uploadFile("archivo", $mercurio01->getPath() . "/galeria");
                    if ($estado != false) {
                        $mercurio56->setArchivo($name);
                        if (!$mercurio56->save()) {
                            parent::setLogger($mercurio56->getMessages());
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
            $codinf = $request->input('codinf', "addslaches", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio56->count("*", "conditions: codinf = '$codinf'");
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
        $_fields["email"] = array('header' => "Email", 'size' => "31", 'align' => "C");
        $_fields["telefono"] = array('header' => "Telefono", 'size' => "31", 'align' => "C");
        $_fields["nota"] = array('header' => "Nota", 'size' => "31", 'align' => "C");
        $_fields["estado"] = array('header' => "Estado", 'size' => "31", 'align' => "C");
        $_fields["archivo"] = array('header' => "Archivo", 'size' => "31", 'align' => "C");
        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio56", $_fields, $this->query, "Firmas", $format);
        return $this->renderObject($file, false);
    }
}
