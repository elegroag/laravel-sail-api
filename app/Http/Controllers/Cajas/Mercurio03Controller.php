<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio03Controller extends ApplicationController
{

    private $query = "1=1";
    private $cantidad_pagina = 0;

    public function __construct()
    {
       
        
        
        $this->cantidad_pagina = $this->numpaginate;
        
    }

    public function beforeFilter($permisos = array())
    {
        $permisos = array("aplicarFiltro" => "9", "editar" => "10", "guardar" => "11", "buscar" => "12", "borrar" => "13");
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
        Core::importLibrary("Table", "Pagination");
        $table = new Table();
        $table->set_template(Table::TmpGeneral());
        $table->set_heading(
            "OPT",
            "Codigo",
            "Nombre",
            "Cargo",
            "Archivo",
            "Email"
        );

        if (count($paginate->items) > 0) {
            foreach ($paginate->items as $mtable) {
                $table->add_row(
                    "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' data-cid='{$mtable->getCodfir()}' data-toggle='editar'>
                        <i class='fas fa-user-edit text-white'></i>
                    </a>
                    <a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' data-cid='{$mtable->getCodfir()}' data-toggle='borrar'>
                        <i class='fas fa-trash text-white'></i>
                    </a>",
                    $mtable->getCodfir(),
                    $mtable->getNombre(),
                    $mtable->getCargo(),
                    $mtable->getArchivo(),
                    $mtable->getEmail()
                );
            }
        } else {
            $table->add_row('');
            $table->set_empty("<tr><td colspan='6'> &nbsp; No hay registros que mostrar</td></tr>");
        }
        return $table->generate();
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
            "codfir" => "Codigo",
            "detalle" => "Detalle",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Firmas");
        $this->setParamToView("buttons", array("N", "F", "R"));
        Tag::setDocumentTitle('Motivos Firmas');
    }


    public function buscarAction()
    {
        $this->setResponse("ajax");
        $pagina = $request->input('pagina');
        if ($pagina == "") $pagina = 1;
        $paginate = Tag::paginate($this->Mercurio03->find("$this->query"), $pagina, $this->cantidad_pagina);
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
            $codfir = $request->input('codfir');
            $mercurio03 = $this->Mercurio03->findFirst("codfir = '$codfir'");
            if ($mercurio03 == false) $mercurio03 = new Mercurio03();
            return $this->renderObject($mercurio03->getArray(), false);
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
                $codfir = $request->input('codfir');
                $modelos = array("Mercurio03");
                
                $response = $this->db->begin();
                $this->Mercurio03->deleteAll("codfir = '$codfir'");
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
                $codfir = $request->input('codfir', "addslaches", "alpha", "extraspaces", "striptags");
                $nombre = $request->input('nombre', "addslaches", "alpha", "extraspaces", "striptags");
                $cargo = $request->input('cargo', "addslaches", "alpha", "extraspaces", "striptags");
                $archivo = $request->input('archivo', "addslaches", "extraspaces", "striptags");
                $email = $request->input('email', "addslaches", "extraspaces", "striptags");
                $modelos = array("Mercurio03");
                
                $response = $this->db->begin();
                $mercurio03 = new Mercurio03();
                $mercurio03->setTransaction($Transaccion);
                $mercurio03->setCodfir($codfir);
                $mercurio03->setNombre($nombre);
                $mercurio03->setCargo($cargo);
                $mercurio03->setEmail($email);
                $mercurio01 = $this->Mercurio01->findFirst();
                if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != "") {
                    $extension = explode(".", $_FILES['archivo']['name']);
                    $name = $codfir . "_firma." . end($extension);
                    $_FILES['archivo']['name'] = $name;
                    $estado = $this->uploadFile("archivo", $mercurio01->getPath());
                    if ($estado != false) {
                        $mercurio03->setArchivo($name);
                        if (!$mercurio03->save()) {
                            parent::setLogger($mercurio03->getMessages());
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
            $codfir = $request->input('codfir', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio03->count("*", "conditions: codfir = '$codfir'");
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
        $_fields["codfir"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["nombre"] = array('header' => "Nombre", 'size' => "31", 'align' => "C");
        $_fields["cargo"] = array('header' => "Cargo", 'size' => "31", 'align' => "C");
        $_fields["archivo"] = array('header' => "Archivo", 'size' => "31", 'align' => "C");
        $_fields["email"] = array('header' => "Email", 'size' => "31", 'align' => "C");

        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio03", $_fields, $this->query, "Firmas", $format);
        return $this->renderObject($file, false);
    }
}
