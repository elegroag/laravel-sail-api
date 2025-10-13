<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio73;
use App\Services\Utils\UploadFile;
use Illuminate\Http\Request;

class Mercurio73Controller extends ApplicationController
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
        return view('cajas.mercurio73.index', [
            'title' => 'Promociones de Educación',
        ]);
    }

    public function galeriaAction()
    {
        try {
            $this->setResponse('ajax');
            $instancePath = env('APP_URL');
            $mercurio01 = $this->Mercurio01->findFirst();
            $con = DbBase::rawConnect();
            $response = $con->inQueryAssoc("SELECT numedu,concat('$instancePath{$mercurio01->getPath()}galeria/',archivo) as archivo FROM mercurio73 ORDER BY orden ASC");
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
            $numedu = $this->Mercurio73->maximum('numedu') + 1;
            $orden = $this->Mercurio73->maximum('orden') + 1;
            $url = $request->input('url');
            $modelos = ['mercurio73'];

            $response = $this->db->begin();
            $mercurio73 = new Mercurio73;

            $mercurio73->setNumedu($numedu);
            $mercurio73->setOrden($orden);
            $mercurio73->setUrl($url);
            $mercurio73->setEstado('A');

            $mercurio01 = $this->Mercurio01->findFirst();

            if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != '') {
                $name = 'promo_educacion' . $numedu . '.' . substr($_FILES['archivo']['name'], -3);
                $_FILES['archivo']['name'] = $name;

                $uploadFile = new UploadFile;
                $uploadFile->upload('archivo', "{$mercurio01->getPath()}galeria");
                $mercurio73->setArchivo($_FILES['archivo']['name']);
            }

            if (! $mercurio73->save()) {
                parent::setLogger($mercurio73->getMessages());
                $this->db->rollback();
            }

            $this->db->commit();
            $response = parent::successFunc('Creacion terminada Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se puede guardar el Registro' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function arribaAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');
            $objetivo = $this->Mercurio73->findFirst("numedu = $numpro");
            $orden_obj = $objetivo->getOrden();
            $minimo = $this->Mercurio73->minimum('orden');

            if ($orden_obj != $minimo) {
                $superior = $this->Mercurio73->findFirst("conditions: orden < $orden_obj", 'order: orden desc');
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
            $objetivo = $this->Mercurio73->findFirst("numedu = $numpro");
            $orden_obj = $objetivo->getOrden();
            $maximo = $this->Mercurio73->maximum('orden');

            if ($orden_obj != $maximo) {
                $inferior = $this->Mercurio73->findFirst("conditions: orden > $orden_obj", 'order: orden asc');
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
            $archivo = $this->Mercurio73->findFirst("numedu = '$numpro'")->getArchivo();
            $mercurio01 = $this->Mercurio01->findFirst();
            if (! empty($archivo) && file_exists("{$mercurio01->getPath()}galeria/" . $archivo)) {
                unlink("{$mercurio01->getPath()}galeria/" . $archivo);
            }

            $response = $this->db->begin();
            $this->Mercurio73->deleteAll("numedu = $numpro");
            $this->db->commit();
            $response = parent::successFunc('Inactivado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se puede Borrar el Registro');

            return $this->renderObject($response, false);
        }
    }
}
