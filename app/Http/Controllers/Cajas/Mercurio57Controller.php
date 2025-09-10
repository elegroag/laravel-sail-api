<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio57Controller extends ApplicationController
{


    public function __construct()
    {
       
        
    }

    public function indexAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Promociones Movil");
        Tag::setDocumentTitle('Promociones Movil');
    }

    public function galeriaAction()
    {
        try {
            $this->setResponse("ajax");
            $instancePath = env('APP_URL');
            $mercurio01 = $this->Mercurio01->findFirst();
            $con = DbBase::rawConnect();
            $response = $con->inQueryAssoc("SELECT numpro,concat('$instancePath{$mercurio01->getPath()}galeria/',archivo) as archivo FROM mercurio57 ORDER BY orden ASC");
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
                $numpro = $this->Mercurio57->maximum("numpro") + 1;
                $orden =  $this->Mercurio57->maximum("orden") + 1;
                $url = $request->input("url");
                $modelos = array("mercurio57");
                
                $response = $this->db->begin();
                $mercurio57 = new Mercurio57();

                $mercurio57->setTransaction($Transaccion);
                $mercurio57->setNumpro($numpro);
                $mercurio57->setOrden($orden);
                $mercurio57->setUrl($url);
                $mercurio57->setEstado('A');

                $mercurio01 = $this->Mercurio01->findFirst();

                if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != "") {
                    $name = "promo_movil_" . $numpro . "." . substr($_FILES['archivo']['name'], -3);
                    $_FILES['archivo']['name'] = $name;
                    $this->uploadFile("archivo", "{$mercurio01->getPath()}galeria");
                    $mercurio57->setArchivo($_FILES['archivo']['name']);
                }

                if (!$mercurio57->save()) {
                    parent::setLogger($mercurio57->getMessages());
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
                $numpro = $request->input('numpro');
                $objetivo = $this->Mercurio57->findFirst("numpro = $numpro");
                $orden_obj = $objetivo->getOrden();
                $minimo =  $this->Mercurio57->minimum("orden");

                if ($orden_obj != $minimo) {
                    $superior = $this->Mercurio57->findFirst("conditions: orden < $orden_obj", "order: orden desc");
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
                $numpro = $request->input('numpro');
                $objetivo = $this->Mercurio57->findFirst("numpro = $numpro");
                $orden_obj = $objetivo->getOrden();
                $maximo =  $this->Mercurio57->maximum("orden");

                if ($orden_obj != $maximo) {
                    $inferior = $this->Mercurio57->findFirst("conditions: orden > $orden_obj", "order: orden asc");
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
                $numpro = $request->input('numpro');
                $archivo = $this->Mercurio57->findFirst("numpro = '$numpro'")->getArchivo();
                $mercurio01 = $this->Mercurio01->findFirst();
                if (!empty($archivo) && file_exists("{$mercurio01->getPath()}galeria/" . $archivo)) {
                    unlink("{$mercurio01->getPath()}galeria/" . $archivo);
                }
                $modelos = array("mercurio57");
                
                $response = $this->db->begin();
                $this->Mercurio57->deleteAll("numpro = $numpro");
                $this->db->commit();
                $response = parent::successFunc("Inactivado Con Exito");
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
