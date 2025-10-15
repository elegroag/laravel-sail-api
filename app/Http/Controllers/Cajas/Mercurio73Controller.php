<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio73;
use App\Services\Utils\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $instancePath = env('APP_URL');
        $mercurio01 = Mercurio01::first();
        $response = Mercurio73::select('numedu', 'archivo')
            ->addSelect(
                DB::raw("concat('{$instancePath}{$mercurio01->getPath()}galeria/', archivo) as archivo")
            )
            ->orderBy('orden', 'asc')
            ->get();
        $this->renderObject($response, false);
    }

    public function guardarAction(Request $request)
    {
        try {
            $numedu = Mercurio73::max('numedu') + 1;
            $orden = Mercurio73::max('orden') + 1;
            $url = $request->input('url');

            $this->db->begin();
            $mercurio73 = new Mercurio73;

            $mercurio73->setNumedu($numedu);
            $mercurio73->setOrden($orden);
            $mercurio73->setUrl($url);
            $mercurio73->setEstado('A');

            $mercurio01 = Mercurio01::first();

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
            $objetivo = Mercurio73::where("numedu", $numpro)->first();
            $orden_obj = $objetivo->getOrden();
            $minimo = Mercurio73::min('orden');

            if ($orden_obj != $minimo) {
                $superior = Mercurio73::whereRaw("orden < $orden_obj")->orderBy('orden', 'desc')->first();
                $orden_sup = $superior->getOrden();
                $objetivo->orden = $orden_sup;
                $objetivo->update();
                $superior->orden = $orden_obj;
                $superior->update();
                $response = 'Ordenado Con Exito';
            } else {
                $response = 'No se puede Ordenar el Registro';
            }

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se puede Ordenar el Registro';
            return $this->renderObject($response, false);
        }
    }

    public function abajoAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');
            $objetivo = Mercurio73::where("numedu", $numpro)->first();
            $orden_obj = $objetivo->getOrden();
            $maximo = Mercurio73::max('orden');

            if ($orden_obj != $maximo) {
                $inferior = Mercurio73::whereRaw("orden > $orden_obj")->orderBy('orden', 'asc')->first();
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
            $archivo = Mercurio73::where("numedu", $numpro)->first()->getArchivo();
            $mercurio01 = Mercurio01::first();
            if (! empty($archivo) && file_exists("{$mercurio01->getPath()}galeria/" . $archivo)) {
                unlink("{$mercurio01->getPath()}galeria/" . $archivo);
            }

            $response = $this->db->begin();
            Mercurio73::where("numedu", $numpro)->delete();
            $this->db->commit();
            $response = 'Inactivado Con Exito';

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se puede Borrar el Registro';
            return $this->renderObject($response, false);
        }
    }
}
