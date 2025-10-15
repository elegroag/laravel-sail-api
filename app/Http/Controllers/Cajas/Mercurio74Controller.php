<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio74;
use App\Services\Utils\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return view('cajas.mercurio74.index', [
            'title' => 'Promociones  de Recreación',
        ]);
    }

    public function galeriaAction()
    {
        try {
            $this->setResponse('ajax');
            $instancePath = env('APP_URL');
            $mercurio01 = Mercurio01::first();
            $response = Mercurio74::select('numrec', 'archivo')
                ->addSelect(
                    DB::raw("concat('{$instancePath}{$mercurio01->getPath()}galeria/', archivo) as archivo")
                )
                ->orderBy('orden', 'asc')
                ->get();
            $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se puede Ordenar el Registro';

            return $this->renderObject($response, false);
        }
    }

    public function guardarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numrec = Mercurio74::max('numrec') + 1;
            $orden = Mercurio74::max('orden') + 1;
            $url = $request->input('url');
            $modelos = ['mercurio74'];

            $response = $this->db->begin();
            $mercurio74 = new Mercurio74;

            $mercurio74->setNumrec($numrec);
            $mercurio74->setOrden($orden);
            $mercurio74->setUrl($url);
            $mercurio74->setEstado('A');

            $mercurio01 = Mercurio01::first();

            if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != '') {
                $name = 'promo_recreacion' . $numrec . '.' . substr($_FILES['archivo']['name'], -3);
                $_FILES['archivo']['name'] = $name;

                $uploadFile = new UploadFile;
                $uploadFile->upload('archivo', "{$mercurio01->getPath()}galeria");
                $mercurio74->setArchivo($_FILES['archivo']['name']);
            }

            if (! $mercurio74->save()) {
                parent::setLogger($mercurio74->getMessages());
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
            $objetivo = Mercurio74::where("numrec", $numpro)->first();
            $orden_obj = $objetivo->getOrden();
            $minimo = Mercurio74::min('orden');

            if ($orden_obj != $minimo) {
                $superior = Mercurio74::whereRaw("orden < $orden_obj")->orderBy('orden', 'desc')->first();
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
            $objetivo = Mercurio74::where("numrec", $numpro)->first();
            $orden_obj = $objetivo->getOrden();
            $maximo = Mercurio74::max('orden');

            if ($orden_obj != $maximo) {
                $inferior = Mercurio74::whereRaw("orden > $orden_obj")->orderBy('orden', 'asc')->first();
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
            $archivo = Mercurio74::where("numrec", $numpro)->first()->getArchivo();
            $mercurio01 = Mercurio01::first();
            if (! empty($archivo) && file_exists("{$mercurio01->getPath()}galeria/" . $archivo)) {
                unlink("{$mercurio01->getPath()}galeria/" . $archivo);
            }

            $response = $this->db->begin();
            Mercurio74::where("numrec", $numpro)->delete();
            $this->db->commit();
            $response = 'Inactivado Con Exito';

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se puede Borrar el Registro';
            return $this->renderObject($response, false);
        }
    }
}
