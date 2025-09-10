<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio53;
use App\Services\Utils\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio53Controller extends ApplicationController
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
        $this->setParamToView("title", "Destacadas");
        # Tag::setDocumentTitle('Destacadas');
    }

    public function galeriaAction()
    {
        try {
            $this->setResponse("ajax");
            $instancePath = env('APP_URL');
            $mercurio01 = $this->Mercurio01->findFirst();
            $con = DbBase::rawConnect();
            $response = $con->inQueryAssoc("SELECT numero,concat('$instancePath{$mercurio01->getPath()}galeria/',archivo) as archivo FROM mercurio53 ORDER BY orden ASC");
            $this->renderObject($response, false);
        } catch (DebugException $e) {
            parent::setLogger($e->getMessage());
            $this->db->rollback();
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

                $response = $this->db->begin();
                $mercurio53 = new Mercurio53();

                $mercurio53->setNumero($numero);
                $mercurio53->setOrden($orden);

                $mercurio01 = $this->Mercurio01->findFirst();

                if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != "") {
                    $name = "promom_" . $numero . "." . substr($_FILES['archivo']['name'], -3);
                    $_FILES['archivo']['name'] = $name;

                    $uploadFile = new UploadFile();
                    $uploadFile->upload("archivo", "{$mercurio01->getPath()}galeria");
                    $mercurio53->setArchivo($_FILES['archivo']['name']);
                }

                if (!$mercurio53->save()) {
                    parent::setLogger($mercurio53->getMessages());
                    $this->db->rollback();
                }

                $this->db->commit();
                $response = parent::successFunc("Creacion terminada Con Exito");
                return $this->renderObject($response, false);
            } catch (DebugException $e) {
                parent::setLogger($e->getMessage());
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede guardar el Registro" . $e->getMessage());
            return $this->renderObject($response, false);
        }
    }

    public function arribaAction(Request $request)
    {
        try {
            try {
                $this->setResponse("ajax");
                $numero = $request->input('numero');
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
            } catch (DebugException $e) {
                parent::setLogger($e->getMessage());
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede Ordenar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function abajoAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $numero = $request->input('numero');
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
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede Ordenar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function borrarAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $numero = $request->input('numero');
            $archivo = $this->Mercurio53->findFirst("numero = '$numero'")->getArchivo();
            $mercurio01 = $this->Mercurio01->findFirst();
            if (!empty($archivo) && file_exists("{$mercurio01->getPath()}galeria/" . $archivo)) {
                unlink("{$mercurio01->getPath()}galeria/" . $archivo);
            }

            $response = $this->db->begin();
            $this->Mercurio53->deleteAll("numero = $numero");
            $this->db->commit();
            $response = parent::successFunc("Borrado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se puede Borrar el Registro");
            return $this->renderObject($response, false);
        }
    }
}
