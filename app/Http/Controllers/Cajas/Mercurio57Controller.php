<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio57;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class Mercurio57Controller extends ApplicationController
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

    public function index()
    {
        return view('cajas.mercurio57.index', [
            'title' => 'Promociones Movil',
            'help' => 'Esta opcion permite manejar las promociones del carrusel móvil.',
        ]);
    }

    public function galeria()
    {
        try {
            $this->setResponse('ajax');
            $mercurio01 = Mercurio01::first();
            if (! $mercurio01) {
                throw new DebugException('Configuración básica no encontrada.');
            }

            $path = url($mercurio01->getPath() . 'galeria');
            $galeria = Mercurio57::where('estado', 'A')->orderBy('orden', 'ASC')->get();

            $response = $galeria->map(function ($item) use ($path) {
                return [
                    'numpro' => $item->numpro,
                    'archivo' => $path . '/' . $item->archivo,
                    'url' => $item->url,
                ];
            });

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc($e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function guardar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $this->db->begin();

            $numpro = (Mercurio57::max('numpro') ?? 0) + 1;
            $orden = (Mercurio57::max('orden') ?? 0) + 1;
            $url = $request->input('url');

            $mercurio57 = new Mercurio57;
            $mercurio57->setNumpro($numpro);
            $mercurio57->setOrden($orden);
            $mercurio57->setUrl($url);
            $mercurio57->setEstado('A');

            $mercurio01 = Mercurio01::first();
            if (! $mercurio01) {
                throw new DebugException('Configuración básica no encontrada.');
            }

            if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
                $file = $request->file('archivo');
                $extension = $file->getClientOriginalExtension();
                $fileName = 'promo_movil_' . $numpro . '.' . $extension;
                $destinationPath = public_path($mercurio01->getPath() . 'galeria');
                $file->move($destinationPath, $fileName);
                $mercurio57->setArchivo($fileName);
            } else {
                throw new DebugException('No se ha subido ningún archivo o el archivo no es válido.');
            }

            if (! $mercurio57->save()) {
                parent::setLogger($mercurio57->getMessages());
                $this->db->rollback();
                throw new DebugException('Error al guardar la promoción.');
            }

            $this->db->commit();
            $response = parent::successFunc('Creacion terminada Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede guardar el Registro: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function arriba(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');

            $this->db->begin();
            $objetivo = Mercurio57::where('numpro', $numpro)->first();
            if (! $objetivo) {
                throw new DebugException('Registro no encontrado.');
            }

            $orden_obj = $objetivo->getOrden();
            $minimo = Mercurio57::min('orden');

            if ($orden_obj != $minimo) {
                $superior = Mercurio57::where('orden', '<', $orden_obj)->orderBy('orden', 'desc')->first();
                if ($superior) {
                    $orden_sup = $superior->getOrden();
                    $objetivo->orden = $orden_sup;
                    $superior->orden = $orden_obj;
                    $objetivo->save();
                    $superior->save();
                }
            }
            $this->db->commit();
            $response = parent::successFunc('Ordenado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede Ordenar el Registro: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function abajo(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');

            $this->db->begin();
            $objetivo = Mercurio57::where('numpro', $numpro)->first();
            if (! $objetivo) {
                throw new DebugException('Registro no encontrado.');
            }

            $orden_obj = $objetivo->getOrden();
            $maximo = Mercurio57::max('orden');

            if ($orden_obj != $maximo) {
                $inferior = Mercurio57::where('orden', '>', $orden_obj)->orderBy('orden', 'asc')->first();
                if ($inferior) {
                    $orden_inf = $inferior->getOrden();
                    $objetivo->orden = $orden_inf;
                    $inferior->orden = $orden_obj;
                    $objetivo->save();
                    $inferior->save();
                }
            }
            $this->db->commit();
            $response = parent::successFunc('Ordenado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede Ordenar el Registro: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function borrar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');

            $this->db->begin();
            $mercurio57 = Mercurio57::where('numpro', $numpro)->first();

            if ($mercurio57) {
                $archivo = $mercurio57->getArchivo();
                $mercurio01 = Mercurio01::first();

                if ($mercurio01 && ! empty($archivo)) {
                    $filePath = public_path($mercurio01->getPath() . 'galeria/' . $archivo);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $mercurio57->delete();
            } else {
                throw new DebugException('El registro a borrar no existe.');
            }

            $this->db->commit();
            $response = parent::successFunc('Borrado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede Borrar el Registro: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }
}
