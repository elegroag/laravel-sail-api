<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio26Controller extends ApplicationController
{


    public function __construct()
    {
       
        
        
    }

    public function beforeFilter($permisos = array())
    {
        $permisos = array("galeria" => "54", "arriba" => "55", "abajo" => "56", "guardar" => "57", "borrar" => "58");
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


    public function indexAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Galería");
        Tag::setDocumentTitle('Galería');
    }

    public function galeriaAction()
    {
        try {
            $this->setResponse("ajax");
            $instancePath = env('APP_URL');
            $mercurio01 = $this->Mercurio01->findFirst();
            $con = DbBase::rawConnect();
            $response = $con->inQueryAssoc("SELECT numero,concat('$instancePath{$mercurio01->getPath()}galeria/',archivo) as archivo, tipo FROM mercurio26 ORDER BY orden ASC");
            $this->renderObject($response, false);
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
                $numero = $this->Mercurio26->maximum("numero") + 1;
                $orden =  $this->Mercurio26->maximum("orden") + 1;
                $tipo = $request->input('tipo');
                $modelos = array("mercurio26");
                
                $response = $this->db->begin();
                $mercurio26 = new Mercurio26();

                $mercurio26->setTransaction($Transaccion);
                $mercurio26->setNumero($numero);
                $mercurio26->setOrden($orden);
                $mercurio26->setTipo($tipo);

                $mercurio01 = $this->Mercurio01->findFirst();

                if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != "") {
                    $name = "promo_" . $numero . "." . substr($_FILES['archivo']['name'], -3);
                    $_FILES['archivo']['name'] = $name;
                    $this->uploadFile("archivo", "{$mercurio01->getPath()}galeria");
                    $mercurio26->setArchivo($_FILES['archivo']['name']);
                }

                if (!$mercurio26->save()) {
                    parent::setLogger($mercurio26->getMessages());
                    $this->db->rollback();
                }

                $this->db->commit();
                $response = parent::successFunc("Creacion terminada Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede guardar el Registro" . $e->getMessage());
            return $this->renderObject($response, false);
        }
    }

    public function arribaAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $numero = $request->input('numero');
                $objetivo = $this->Mercurio26->findFirst("numero = $numero");
                $orden_obj = $objetivo->getOrden();
                $minimo =  $this->Mercurio26->minimum("orden");

                if ($orden_obj != $minimo) {
                    $superior = $this->Mercurio26->findFirst("conditions: orden < $orden_obj", "order: orden desc");
                    $orden_sup = $superior->getOrden();
                    $objetivo->orden = $orden_sup;
                    $objetivo->update();
                    $superior->orden = $orden_obj;
                    $superior->update();
                    $response = parent::successFunc("Ordenado Con Exito");
                } else {
                    $response = parent::successFunc("No se puede Ordenar el Registro");
                }

                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede Ordenar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function abajoAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $numero = $request->input('numero');
                $objetivo = $this->Mercurio26->findFirst("numero = $numero");
                $orden_obj = $objetivo->getOrden();
                $maximo =  $this->Mercurio26->maximum("orden");

                if ($orden_obj != $maximo) {
                    $inferior = $this->Mercurio26->findFirst("conditions: orden > $orden_obj", "order: orden asc");
                    $orden_inf = $inferior->getOrden();

                    $objetivo->orden = $orden_inf;
                    $objetivo->update();
                    $inferior->orden = $orden_obj;
                    $inferior->update();

                    $response = parent::successFunc("Ordenado Con Exito");
                } else {
                    $response = parent::successFunc("No se puede Ordenar el Registro");
                }

                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede Ordenar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function borrarAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $numero = $request->input('numero');
                $archivo = $this->Mercurio26->findFirst("numero = '$numero'")->getArchivo();
                $mercurio01 = $this->Mercurio01->findFirst();
                if (!empty($archivo) && file_exists("{$mercurio01->getPath()}galeria/" . $archivo)) {
                    unlink("{$mercurio01->getPath()}galeria/" . $archivo);
                }
                $modelos = array("mercurio26");
                
                $response = $this->db->begin();
                $this->Mercurio26->deleteAll("numero = $numero");
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
}
