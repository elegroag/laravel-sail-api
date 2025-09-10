<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio53Controller extends ApplicationController
{


    public function initialize()
    {
        Core::importLibrary("Services", "Services");
        $this->setTemplateAfter('main');
    }

    public function indexAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Destacadas");
        Tag::setDocumentTitle('Destacadas');
    }

    public function galeriaAction()
    {
        try {
            $this->setResponse("ajax");
            $instancePath = Core::getInstancePath();
            $mercurio01 = $this->Mercurio01->findFirst();
            $con = DbBase::rawConnect();
            $response = $con->inQueryAssoc("SELECT numero,concat('$instancePath{$mercurio01->getPath()}galeria/',archivo) as archivo FROM mercurio53 ORDER BY orden ASC");
            $this->renderObject($response, false);
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
                $numero = $this->Mercurio53->maximum("numero") + 1;
                $orden =  $this->Mercurio53->maximum("orden") + 1;
                $modelos = array("mercurio53");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $mercurio53 = new Mercurio53();

                $mercurio53->setTransaction($Transaccion);
                $mercurio53->setNumero($numero);
                $mercurio53->setOrden($orden);

                $mercurio01 = $this->Mercurio01->findFirst();

                if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != "") {
                    $name = "promom_" . $numero . "." . substr($_FILES['archivo']['name'], -3);
                    $_FILES['archivo']['name'] = $name;
                    $this->uploadFile("archivo", "{$mercurio01->getPath()}galeria");
                    $mercurio53->setArchivo($_FILES['archivo']['name']);
                }

                if (!$mercurio53->save()) {
                    parent::setLogger($mercurio53->getMessages());
                    parent::ErrorTrans();
                }

                parent::finishTrans();
                $response = parent::successFunc("Creacion terminada Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                parent::ErrorTrans();
            }
        } catch (TransactionFailed $e) {
            $response = parent::errorFunc("No se puede guardar el Registro" . $e->getMessage());
            return $this->renderObject($response, false);
        }
    }

    public function arribaAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $numero = $this->getPostParam('numero');
                $objetivo = $this->Mercurio53->findFirst("numero = $numero");
                $orden_obj = $objetivo->getOrden();
                $minimo =  $this->Mercurio53->minimum("orden");
                if ($orden_obj != $minimo) {
                    $superior = $this->Mercurio53->findFirst("conditions: orden < $orden_obj", "order: orden desc");
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
                parent::ErrorTrans();
            }
        } catch (TransactionFailed $e) {
            $response = parent::errorFunc("No se puede Ordenar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function abajoAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $numero = $this->getPostParam('numero');
                $objetivo = $this->Mercurio53->findFirst("numero = $numero");
                $orden_obj = $objetivo->getOrden();
                $maximo =  $this->Mercurio53->maximum("orden");

                if ($orden_obj != $maximo) {
                    $inferior = $this->Mercurio53->findFirst("conditions: orden > $orden_obj", "order: orden asc");
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
                parent::ErrorTrans();
            }
        } catch (TransactionFailed $e) {
            $response = parent::errorFunc("No se puede Ordenar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function borrarAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $numero = $this->getPostParam('numero');
                $archivo = $this->Mercurio53->findFirst("numero = '$numero'")->getArchivo();
                $mercurio01 = $this->Mercurio01->findFirst();
                if (!empty($archivo) && file_exists("{$mercurio01->getPath()}galeria/" . $archivo)) {
                    unlink("{$mercurio01->getPath()}galeria/" . $archivo);
                }
                $modelos = array("mercurio53");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $this->Mercurio53->deleteAll("numero = $numero");
                parent::finishTrans();
                $response = parent::successFunc("Borrado Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                parent::ErrorTrans();
            }
        } catch (TransactionFailed $e) {
            $response = parent::errorFunc("No se puede Borrar el Registro");
            return $this->renderObject($response, false);
        }
    }
}
