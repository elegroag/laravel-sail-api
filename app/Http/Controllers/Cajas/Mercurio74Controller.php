<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio74Controller extends ApplicationController
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
        $this->setParamToView("title", "Promociones  de Recreación");
        Tag::setDocumentTitle('Promociones de Recreación');
    }

    public function galeriaAction()
    {
        try {
            $this->setResponse("ajax");
            $instancePath = Core::getInstancePath();
            $mercurio01 = $this->Mercurio01->findFirst();
            $con = DbBase::rawConnect();
            $response = $con->inQueryAssoc("SELECT numrec,concat('$instancePath{$mercurio01->getPath()}galeria/',archivo) as archivo FROM mercurio74 ORDER BY orden ASC");
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
                $numrec = $this->Mercurio74->maximum("numrec") + 1;
                $orden =  $this->Mercurio74->maximum("orden") + 1;
                $url = $this->getPostParam("url");
                $modelos = array("mercurio74");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $mercurio74 = new Mercurio74();

                $mercurio74->setTransaction($Transaccion);
                $mercurio74->setNumrec($numrec);
                $mercurio74->setOrden($orden);
                $mercurio74->setUrl($url);
                $mercurio74->setEstado('A');

                $mercurio01 = $this->Mercurio01->findFirst();

                if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != "") {
                    $name = "promo_recreacion" . $numrec . "." . substr($_FILES['archivo']['name'], -3);
                    $_FILES['archivo']['name'] = $name;
                    $this->uploadFile("archivo", "{$mercurio01->getPath()}galeria");
                    $mercurio74->setArchivo($_FILES['archivo']['name']);
                }

                if (!$mercurio74->save()) {
                    parent::setLogger($mercurio74->getMessages());
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
                $numpro = $this->getPostParam('numpro');
                $objetivo = $this->Mercurio74->findFirst("numrec = $numpro");
                $orden_obj = $objetivo->getOrden();
                $minimo =  $this->Mercurio74->minimum("orden");

                if ($orden_obj != $minimo) {
                    $superior = $this->Mercurio74->findFirst("conditions: orden < $orden_obj", "order: orden desc");
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
                $numpro = $this->getPostParam('numpro');
                $objetivo = $this->Mercurio74->findFirst("numrec = $numpro");
                $orden_obj = $objetivo->getOrden();
                $maximo =  $this->Mercurio74->maximum("orden");

                if ($orden_obj != $maximo) {
                    $inferior = $this->Mercurio74->findFirst("conditions: orden > $orden_obj", "order: orden asc");
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
                $numpro = $this->getPostParam('numpro');
                $archivo = $this->Mercurio74->findFirst("numrec = '$numpro'")->getArchivo();
                $mercurio01 = $this->Mercurio01->findFirst();
                if (!empty($archivo) && file_exists("{$mercurio01->getPath()}galeria/" . $archivo)) {
                    unlink("{$mercurio01->getPath()}galeria/" . $archivo);
                }
                $modelos = array("mercurio74");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $this->Mercurio74->deleteAll("numrec = $numpro");
                parent::finishTrans();
                $response = parent::successFunc("Inactivado Con Exito");
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
