<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio04Controller extends ApplicationController
{

    private $query = "1=1";
    private $cantidad_pagina = 0;

    public function __construct()
    {
       
        
        
        $this->cantidad_pagina = $this->numpaginate;
        
    }

    public function beforeFilter($permisos = array())
    {
        $permisos = array("aplicarFiltro" => "49", "editar" => "50", "guardar" => "51", "buscar" => "52", "borrar" => "53");
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
        $html .= "<th scope='col'>Codigo</th>";
        $html .= "<th scope='col'>Detalle</th>";
        $html .= "<th scope='col'>Principal</th>";
        $html .= "<th scope='col'>Estado</th>";
        $html .= "<th scope='col'>Options</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getCodofi()}</td>";
            $html .= "<td>{$mtable->getDetalle()}</td>";
            $html .= "<td>{$mtable->getPrincipalDetalle()}</td>";
            $html .= "<td>{$mtable->getEstadoDetalle()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='btn btn-xs btn-primary' data-cid='{$mtable->getCodofi()}' data-toggle='ciudad-view'>";
            $html .= "<i class='fas fa-city'></i>";
            $html .= "</a>&nbsp;";
            $html .= "<a href='#!' class='btn btn-xs btn-success' data-cid='{$mtable->getCodofi()}' data-toggle='opcion-view'>";
            $html .= "<i class='fas fa-clipboard-list text-white'></i>";
            $html .= "</a>&nbsp;";
            $html .= "<a href='#!' class='btn btn-xs btn-warning' data-cid='{$mtable->getCodofi()}' data-toggle='editar'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>&nbsp;";
            $html .= "<a href='#!' class='btn btn-xs btn-danger' data-cid='{$mtable->getCodofi()}' data-toggle='borrar'>";
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
            "codofi" => "Codigo",
            "detalle" => "Detalle",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Oficinas");
        $this->setParamToView("buttons", array("N", "F", "R"));
        Tag::setDocumentTitle('Motivos Oficinas');

        $ps = Comman::Api();
        $ps->runCli(
            array(
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'listar_ciudades_departamentos',
                'params' => false
            )
        );
        $out = $ps->toArray();
        $_codciu = array();
        foreach ($out['ciudades'] as $mcodciu) {
            $_codciu[$mcodciu['codciu']] = $mcodciu['detciu'];
        }
        $this->setParamToView("ciudades", $_codciu);
    }


    public function buscarAction()
    {
        $this->setResponse("ajax");
        $pagina = $request->input('pagina');
        if ($pagina == "") $pagina = 1;
        $paginate = Tag::paginate($this->Mercurio04->find("$this->query"), $pagina, $this->cantidad_pagina);
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
            $codofi = $request->input('codofi');
            $mercurio04 = $this->Mercurio04->findFirst("codofi = '$codofi'");
            if ($mercurio04 == false) $mercurio04 = new Mercurio04();

            return  $this->renderObject($mercurio04->getArray(), false);
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
                $codofi = $request->input('codofi');
                $modelos = array("Mercurio04");
                
                $response = $this->db->begin();
                $this->Mercurio04->deleteAll("codofi = '$codofi'");
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
                $codofi = $request->input('codofi', "addslaches", "alpha", "extraspaces", "striptags");
                $detalle = $request->input('detalle', "addslaches", "alpha", "extraspaces", "striptags");
                $principal = $request->input('principal', "addslaches", "alpha", "extraspaces", "striptags");
                $estado = $request->input('estado', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("Mercurio04");
                
                $response = $this->db->begin();
                $mercurio04 = new Mercurio04();
                $mercurio04->setTransaction($Transaccion);
                $mercurio04->setCodofi($codofi);
                $mercurio04->setDetalle($detalle);
                $mercurio04->setPrincipal($principal);
                $mercurio04->setEstado($estado);
                if (!$mercurio04->save()) {
                    parent::setLogger($mercurio04->getMessages());
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

    public function validePkAction()
    {
        try {
            $this->setResponse("ajax");
            $codofi = $request->input('codofi', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio04->count("*", "conditions: codofi = '$codofi'");
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
        $_fields["codofi"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["detalle"] = array('header' => "Detalle", 'size' => "31", 'align' => "C");
        $_fields["principal"] = array('header' => "Principal", 'size' => "31", 'align' => "C");
        $_fields["estado"] = array('header' => "Estado", 'size' => "31", 'align' => "C");
        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio04", $_fields, $this->query, "Oficinas", $format);
        return $this->renderObject($file, false);
    }

    public function validePkCiudadAction()
    {
        try {
            $this->setResponse("ajax");
            $codofi = $request->input('codofi', "addslaches", "alpha", "extraspaces", "striptags");
            $codciu = $request->input('codciu', "addslaches", "alpha", "extraspaces", "striptags");
            $response = parent::successFunc("");
            $l = $this->Mercurio05->count("*", "conditions: codofi = '$codofi' and codciu='$codciu'");
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

    public function ciudad_viewAction()
    {
        try {
            $this->setResponse("ajax");
            $codofi = $request->input('codofi', "addslaches", "alpha", "extraspaces", "striptags");
            $response = "";
            $mercurio05 = $this->Mercurio05->find("codofi='$codofi'");
            foreach ($mercurio05 as $mmercurio05) {
                $value = "";
                $response .= "<tr>";
                $response .= "<td>" . $mmercurio05->getCodciu() . "</td>";
                $response .= "<td class='table-actions'>";
                $response .= "<a href='#!' class='table-action btn btn-xs btn-primary' data-toggle='ciudad-borrar' data-codofi='{$codofi}' data-codciu='{$mmercurio05->getCodciu()}'>";
                $response .= "<i class='fas fa-trash text-white'></i>";
                $response .= "</a>";
                $response .= "</td>";
                $response .= "</tr>";
            }
            return $this->renderObject($response, false);
        } catch (DbException $e) {
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function guardarCiudadAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $codofi = $request->input('codofi', "addslaches", "alpha", "extraspaces", "striptags");
                $codciu = $request->input('codciu', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("mercurio05");
                
                $response = $this->db->begin();
                $mercurio05 = new Mercurio05();
                $mercurio05->setTransaction($Transaccion);
                $mercurio05->setCodofi($codofi);
                $mercurio05->setCodciu($codciu);
                if (!$mercurio05->save()) {
                    parent::setLogger($mercurio05->getMessages());
                    $this->db->rollback();
                }
                $this->db->commit();
                $response = parent::successFunc("Movimiento Realizado Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function editarCiudadAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $codofi = $request->input('codofi', "addslaches", "alpha", "extraspaces", "striptags");
                $codciu = $request->input('codciu', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("mercurio28");
                
                $response = $this->db->begin();
                $mercurio05 = $this->Mercurio05->findFirst("codofi='$codofi' and codciu = '$codciu'");
                if ($mercurio05 == false) $mercurio05 = new Mercurio05();

                return $this->renderObject($mercurio05->getArray(), false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function borrarCiudadAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $codofi = $request->input('codofi', "addslaches", "alpha", "extraspaces", "striptags");
                $codciu = $request->input('codciu', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("mercurio28");
                
                $response = $this->db->begin();
                $this->Mercurio05->deleteAll("codofi='$codofi' and codciu='$codciu'");
                $this->db->commit();
                $response = parent::successFunc("Movimiento Realizado Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }



    public function validePkOpcionAction()
    {
        try {
            $this->setResponse("ajax");
            $codofi = $request->input('codofi', "addslaches", "alpha", "extraspaces", "striptags");
            $tipopc = $request->input('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
            $usuario = $request->input('usuario', "addslaches", "alpha", "extraspaces", "striptags");

            $l = (new Mercurio08)->count(
                "*",
                "conditions: codofi='{$codofi}' AND tipopc='{$tipopc}' AND usuario='{$usuario}'"
            );
            if ($l > 0) {
                throw new DebugException("El Registro ya se encuentra digitado", 501);
            }
            $response = array(
                'flag' => true,
                'msg' => "Ok",
                'success' => true
            );
        } catch (Exception $e) {
            $response = array(
                'flag' => false,
                'msg' => $e->getMessage(),
                'success' => false
            );
        }

        return $this->renderObject($response, false);
    }

    public function opcion_viewAction()
    {
        try {
            $this->setResponse("ajax");
            $codofi = $request->input('codofi', "addslaches", "alpha", "extraspaces", "striptags");
            $response = "";
            $mercurio08 = $this->Mercurio08->find("codofi='$codofi'");
            foreach ($mercurio08 as $mmercurio08) {
                $value = "";
                $response .= "<tr>";
                $response .= "<td>" . $mmercurio08->getTipopcDetalle() . "</td>";
                $response .= "<td>" . $mmercurio08->getUsuarioDetalle() . "</td>";
                $response .= "<td class='table-actions'>";
                $response .= "<a href='#!' class='table-action btn btn-xs btn-primary' " .
                    " data-toggle='opcion-borrar' data-codofi='{$codofi}' data-tipopc='{$mmercurio08->getTipopc()}' data-usuario='{$mmercurio08->getUsuario()}'>";
                $response .= "<i class='fas fa-trash text-white'></i>";
                $response .= "</a>";
                $response .= "</td>";
                $response .= "</tr>";
            }
            return $this->renderObject($response, false);
        } catch (DbException $e) {
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function guardarOpcionAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $codofi = $request->input('codofi', "addslaches", "alpha", "extraspaces", "striptags");
                $tipopc = $request->input('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
                $usuario = $request->input('usuario', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("mercurio08");
                
                $response = $this->db->begin();
                $mercurio08 = $this->Mercurio08->findFirst("codofi='$codofi' and tipopc='$tipopc' and usuario='$usuario'");
                if ($mercurio08 == false) {
                    $mercurio08 = new Mercurio08();
                    $orden = $this->Mercurio08->maximum("orden", "conditions: codofi='$codofi' and tipopc='$tipopc'") + 1;
                    $mercurio08->setOrden($orden);
                }
                $mercurio08->setTransaction($Transaccion);
                $mercurio08->setCodofi($codofi);
                $mercurio08->setTipopc($tipopc);
                $mercurio08->setUsuario($usuario);
                if (!$mercurio08->save()) {
                    parent::setLogger($mercurio08->getMessages());
                    $this->db->rollback();
                }
                $this->db->commit();
                $response = parent::successFunc("Movimiento Realizado Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function borrarOpcionAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $codofi = $request->input('codofi', "addslaches", "alpha", "extraspaces", "striptags");
                $tipopc = $request->input('tipopc', "addslaches", "alpha", "extraspaces", "striptags");
                $usuario = $request->input('usuario', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("mercurio08");
                
                $response = $this->db->begin();
                $this->Mercurio08->deleteAll("codofi='$codofi' and tipopc='$tipopc' and usuario='$usuario'");
                $this->db->commit();
                $response = parent::successFunc("Movimiento Realizado Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }
}
