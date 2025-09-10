<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio58Controller extends ApplicationController
{

    private $query = "1=1";
    private $cantidad_pagina = 0;

    public function __construct()
    {
       
        
        
        $this->cantidad_pagina = $this->numpaginate;
    }

    public function indexAction($codare)
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Archivos Areas");
        $this->setParamToView("codare", $codare);
        Tag::displayTo("codare", $codare);
        Tag::setDocumentTitle('Archivos Areas');
    }

    public function galeriaAction()
    {
        try {
            $this->setResponse("ajax");
            $codare = $request->input("codare");
            $instancePath = env('APP_URL');
            $mercurio01 = $this->Mercurio01->findFirst();
            $con = DbBase::rawConnect();
            $response = $con->inQueryAssoc("SELECT numero,concat('$instancePath{$mercurio01->getPath()}galeria/',archivo) as archivo FROM mercurio58 where codare='$codare' ORDER BY orden ASC");
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
                $codare = $request->input("codare");
                $numero = $this->Mercurio58->maximum("numero") + 1;
                $orden =  $this->Mercurio58->maximum("orden") + 1;
                $modelos = array("mercurio58");
                
                $response = $this->db->begin();
                $mercurio58 = new Mercurio58();

                $mercurio58->setTransaction($Transaccion);
                $mercurio58->setNumero($numero);
                $mercurio58->setCodare($codare);
                $mercurio58->setOrden($orden);

                $mercurio01 = $this->Mercurio01->findFirst();

                if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != "") {
                    $name = "area_{$codare}_" . $numero . "." . substr($_FILES['archivo']['name'], -3);
                    $_FILES['archivo']['name'] = $name;
                    $this->uploadFile("archivo", "{$mercurio01->getPath()}galeria");
                    $mercurio58->setArchivo($_FILES['archivo']['name']);
                }

                if (!$mercurio58->save()) {
                    parent::setLogger($mercurio58->getMessages());
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
                $objetivo = $this->Mercurio58->findFirst("numero = $numero");
                $orden_obj = $objetivo->getOrden();
                $minimo =  $this->Mercurio58->minimum("orden");
                if ($orden_obj != $minimo) {
                    $superior = $this->Mercurio58->findFirst("conditions: orden < $orden_obj", "order: orden desc");
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
                $objetivo = $this->Mercurio58->findFirst("numero = $numero");
                $orden_obj = $objetivo->getOrden();
                $maximo =  $this->Mercurio58->maximum("orden");

                if ($orden_obj != $maximo) {
                    $inferior = $this->Mercurio58->findFirst("conditions: orden > $orden_obj", "order: orden asc");
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
                $archivo = $this->Mercurio58->findFirst("numero = '$numero'")->getArchivo();
                $mercurio01 = $this->Mercurio01->findFirst();
                if (!empty($archivo) && file_exists("{$mercurio01->getPath()}galeria/" . $archivo)) {
                    unlink("{$mercurio01->getPath()}galeria/" . $archivo);
                }
                $modelos = array("mercurio58");
                
                $response = $this->db->begin();
                $this->Mercurio58->deleteAll("numero = $numero");
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
