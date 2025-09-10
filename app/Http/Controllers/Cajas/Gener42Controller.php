<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Gener42Controller extends ApplicationController
{

    protected $query = "1=1";
    protected $cantidad_pagina = 0;

    public function beforeFilter($permisos = array())
    {
        $permisos = array("guardar" => "32", "buscar" => "33");
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

    public function __construct()
    {
       
        
        
        $this->cantidad_pagina = $this->numpaginate;
    }

    public function table($table, $tipo)
    {
        ob_start();
        View::renderView('gener42/asigna_permisos', array('table' => $table, 'tipo' => $tipo));
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function buscarAction()
    {
        $this->setResponse("ajax");
        $usuario = $request->input("usuario");
        $buscar = $request->input("buscar");
        $tipo = $request->input("tipo");
        $response['flag'] = true;

        $likes = "";
        if ($tipo == 'S') {
            $likes = " and detalle like '%{$buscar}%'";
        }
        $table = (new Gener40)->find(
            "codigo IN(select permiso from gener42 where usuario={$usuario}) {$likes}",
            "order: orden"
        );
        $response['permite'] =  $this->table($table, "S");

        $liken = "";
        if ($tipo == 'N') {
            $liken = " and detalle like '%{$buscar}%'";
        }
        $table = (new Gener40)->find(
            "codigo NOT IN(select permiso from gener42 where usuario={$usuario}) {$liken}",
            "order: orden"
        );
        $response['nopermite'] = $this->table($table, "N");
        $this->renderObject($response, false);
    }

    public function indexAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Permisos por usuario");
        //$this->setParamToView("buttons",array("N"));
        Tag::setDocumentTitle('Permisos por usuario');
    }



    public function guardarAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $tipo = $request->input('tipo', "addslaches", "extraspaces", "striptags");
                $usuario = $request->input('usuario', "addslaches", "extraspaces", "striptags");
                $permisos = $request->input('permisos', "addslaches", "extraspaces", "striptags");
                $permisos = explode(";", $permisos);
                $modelos = array("gener42");
                
                $response = $this->db->begin();
                if ($tipo == "A") {
                    foreach ($permisos as $permiso) {
                        if (empty($permiso)) continue;
                        $table = new Gener42();
                        $table->setTransaction($Transaccion);
                        $table->setUsuario($usuario);
                        $table->setPermiso($permiso);
                        if (!$table->save()) {
                            parent::setLogger($table->getMessages());
                            $this->db->rollback();
                        }
                    }
                }
                if ($tipo == "E") {
                    foreach ($permisos as $permiso) {
                        if (empty($permiso)) continue;
                        $this->Gener42->deleteAll("usuario='$usuario' and permiso='$permiso'");
                    }
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
