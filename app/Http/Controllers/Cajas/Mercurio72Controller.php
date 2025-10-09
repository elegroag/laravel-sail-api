<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio72;
use App\Services\Utils\UploadFile;
use Illuminate\Http\Request;

class Mercurio72Controller extends ApplicationController
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
        $help = 'Esta opcion permite manejar los ';
        $this->setParamToView('help', $help);
        $this->setParamToView('title', 'Promociones de Turismo');
        // Tag::setDocumentTitle('Promociones de Turismo');
    }

    public function galeriaAction()
    {
        try {
            $this->setResponse('ajax');
            $instancePath = env('APP_URL');
            $mercurio01 = $this->Mercurio01->findFirst();
            $con = DbBase::rawConnect();
            $response = $con->inQueryAssoc("SELECT numtur,concat('$instancePath{$mercurio01->getPath()}galeria/',archivo) as archivo FROM mercurio72 ORDER BY orden ASC");
            $this->renderObject($response, false);
        } catch (DebugException $e) {
            parent::setLogger($e->getMessage());
            $this->db->rollback();
        }
    }

    public function guardarAction(Request $request)
    {
        try {

            $this->setResponse('ajax');
            $numtur = $this->Mercurio72->maximum('numtur') + 1;
            $orden = $this->Mercurio72->maximum('orden') + 1;
            $url = $request->input('url');

            $response = $this->db->begin();
            $mercurio72 = new Mercurio72;

            $mercurio72->setNumtur($numtur);
            $mercurio72->setOrden($orden);
            $mercurio72->setUrl($url);
            $mercurio72->setEstado('A');

            $mercurio01 = $this->Mercurio01->findFirst();

            if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != '') {
                $name = 'promo_turismo'.$numtur.'.'.substr($_FILES['archivo']['name'], -3);
                $_FILES['archivo']['name'] = $name;

                $uploadFile = new UploadFile;
                $uploadFile->upload('archivo', "{$mercurio01->getPath()}galeria");
                $mercurio72->setArchivo($_FILES['archivo']['name']);
            }

            if (! $mercurio72->save()) {
                parent::setLogger($mercurio72->getMessages());
                $this->db->rollback();
            }

            $this->db->commit();
            $response = parent::successFunc('Creacion terminada Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se puede guardar el Registro'.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function arribaAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');
            $objetivo = $this->Mercurio72->findFirst("numtur = $numpro");
            $orden_obj = $objetivo->getOrden();
            $minimo = $this->Mercurio72->minimum('orden');

            if ($orden_obj != $minimo) {
                $superior = $this->Mercurio72->findFirst("conditions: orden < $orden_obj", 'order: orden desc');
                $orden_sup = $superior->getOrden();
                $objetivo->orden = $orden_sup;
                $objetivo->update();
                $superior->orden = $orden_obj;
                $superior->update();
                $response = parent::successFunc('Ordenado Con Exito');
            } else {
                $response = parent::successFunc('No se puede Ordenar el Registro');
            }

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se puede Ordenar el Registro');

            return $this->renderObject($response, false);
        }
    }

    public function abajoAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');
            $objetivo = $this->Mercurio72->findFirst("numtur = $numpro");
            $orden_obj = $objetivo->getOrden();
            $maximo = $this->Mercurio72->maximum('orden');

            if ($orden_obj != $maximo) {
                $inferior = $this->Mercurio72->findFirst("conditions: orden > $orden_obj", 'order: orden asc');
                $orden_inf = $inferior->getOrden();

                $objetivo->orden = $orden_inf;
                $objetivo->update();
                $inferior->orden = $orden_obj;
                $inferior->update();

                $response = parent::successFunc('Ordenado Con Exito');
            } else {
                $response = parent::successFunc('No se puede Ordenar el Registro');
            }

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se puede Ordenar el Registro');

            return $this->renderObject($response, false);
        }
    }

    public function borrarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');
            $archivo = $this->Mercurio72->findFirst("numtur = '$numpro'")->getArchivo();
            $mercurio01 = $this->Mercurio01->findFirst();
            if (! empty($archivo) && file_exists("{$mercurio01->getPath()}galeria/".$archivo)) {
                unlink("{$mercurio01->getPath()}galeria/".$archivo);
            }
            $modelos = ['mercurio72'];

            $response = $this->db->begin();
            $this->Mercurio72->deleteAll("numtur = $numpro");
            $this->db->commit();
            $response = parent::successFunc('Inactivado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se puede Borrar el Registro');

            return $this->renderObject($response, false);
        }
    }
}
