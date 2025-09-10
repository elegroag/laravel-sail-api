<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio74;
use App\Services\Utils\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio74Controller extends ApplicationController
{

    protected $db;
    protected $user;
    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function indexAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Promociones  de Recreación");
        #Tag::setDocumentTitle('Promociones de Recreación');
    }

    public function galeriaAction()
    {
        try {
            $this->setResponse("ajax");
            $instancePath = env('APP_URL');
            $mercurio01 = $this->Mercurio01->findFirst();
            $con = DbBase::rawConnect();
            $response = $con->inQueryAssoc("SELECT numrec,concat('$instancePath{$mercurio01->getPath()}galeria/',archivo) as archivo FROM mercurio74 ORDER BY orden ASC");
            $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede Ordenar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function guardarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $numrec = $this->Mercurio74->maximum("numrec") + 1;
            $orden =  $this->Mercurio74->maximum("orden") + 1;
            $url = $request->input("url");
            $modelos = array("mercurio74");

            $response = $this->db->begin();
            $mercurio74 = new Mercurio74();

            $mercurio74->setNumrec($numrec);
            $mercurio74->setOrden($orden);
            $mercurio74->setUrl($url);
            $mercurio74->setEstado('A');

            $mercurio01 = $this->Mercurio01->findFirst();

            if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != "") {
                $name = "promo_recreacion" . $numrec . "." . substr($_FILES['archivo']['name'], -3);
                $_FILES['archivo']['name'] = $name;

                $uploadFile = new UploadFile();
                $uploadFile->upload("archivo", "{$mercurio01->getPath()}galeria");
                $mercurio74->setArchivo($_FILES['archivo']['name']);
            }

            if (!$mercurio74->save()) {
                parent::setLogger($mercurio74->getMessages());
                $this->db->rollback();
            }

            $this->db->commit();
            $response = parent::successFunc("Creacion terminada Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede guardar el Registro" . $e->getMessage());
            return $this->renderObject($response, false);
        }
    }

    public function arribaAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $numpro = $request->input('numpro');
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
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede Ordenar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function abajoAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $numpro = $request->input('numpro');
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
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede Ordenar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function borrarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $numpro = $request->input('numpro');
            $archivo = $this->Mercurio74->findFirst("numrec = '$numpro'")->getArchivo();
            $mercurio01 = $this->Mercurio01->findFirst();
            if (!empty($archivo) && file_exists("{$mercurio01->getPath()}galeria/" . $archivo)) {
                unlink("{$mercurio01->getPath()}galeria/" . $archivo);
            }


            $response = $this->db->begin();
            $this->Mercurio74->deleteAll("numrec = $numpro");
            $this->db->commit();
            $response = parent::successFunc("Inactivado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede Borrar el Registro");
            return $this->renderObject($response, false);
        }
    }
}
