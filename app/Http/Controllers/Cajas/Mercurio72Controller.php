<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio72;
use App\Services\Utils\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return view('cajas.mercurio72.index', [
            'title' => 'Promociones de Turismo',
        ]);
    }

    public function galeriaAction()
    {
        try {
            $this->setResponse('ajax');
            $instancePath = env('APP_URL');
            $mercurio01 = Mercurio01::first();
            $response = Mercurio72::select('numtur', 'archivo')
                ->addSelect(
                    DB::raw("concat('{$instancePath}{$mercurio01->getPath()}galeria/', archivo) as archivo")
                )
                ->orderBy('orden', 'asc')
                ->get();

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
            $numtur = Mercurio72::max('numtur') + 1;
            $orden = Mercurio72::max('orden') + 1;
            $url = $request->input('url');

            $response = $this->db->begin();
            $mercurio72 = new Mercurio72;

            $mercurio72->setNumtur($numtur);
            $mercurio72->setOrden($orden);
            $mercurio72->setUrl($url);
            $mercurio72->setEstado('A');

            $mercurio01 = Mercurio01::first();

            if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != '') {
                $name = 'promo_turismo' . $numtur . '.' . substr($_FILES['archivo']['name'], -3);
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
            $response = parent::errorFunc('No se puede guardar el Registro' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function arribaAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');
            $objetivo = Mercurio72::where('numtur', $numpro)->first();
            $orden_obj = $objetivo->getOrden();
            $minimo = Mercurio72::min('orden');

            if ($orden_obj != $minimo) {
                $superior = Mercurio72::where('orden', '<', $orden_obj)->orderBy('orden', 'desc')->first();
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
            $objetivo = Mercurio72::where('numtur', $numpro)->first();
            $orden_obj = $objetivo->getOrden();
            $maximo = Mercurio72::max('orden');

            if ($orden_obj != $maximo) {
                $inferior = Mercurio72::where('orden', '>', $orden_obj)->orderBy('orden', 'asc')->first();
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
            $archivo = Mercurio72::where('numtur', $numpro)->first()->getArchivo();
            $mercurio01 = Mercurio01::first();
            if (! empty($archivo) && file_exists("{$mercurio01->getPath()}galeria/" . $archivo)) {
                unlink("{$mercurio01->getPath()}galeria/" . $archivo);
            }

            $response = $this->db->begin();
            Mercurio72::where('numtur', $numpro)->delete();
            $this->db->commit();
            $response = 'Inactivado Con Exito';

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se puede Borrar el Registro';

            return $this->renderObject($response, false);
        }
    }
}
