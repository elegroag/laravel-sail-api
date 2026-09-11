<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use Illuminate\Support\Facades\DB;
use App\Models\Mercurio01;
use App\Models\Mercurio73;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class Mercurio73Controller extends ApplicationController
{

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function index()
    {
        return view('cajas.mercurio73.index', [
            'title' => 'Promociones de Educación',
        ]);
    }

    public function galeria()
    {
        try {
            $mercurio01 = Mercurio01::first();
            if (! $mercurio01) {
                throw new DebugException('Configuración básica no encontrada.');
            }

            $path = $mercurio01->publicUrl('galeria');
            $galeria = Mercurio73::where('estado', 'A')->orderBy('orden', 'ASC')->get();

            $data = $galeria->map(function ($item) use ($path) {
                return [
                    'numedu' => $item->numedu,
                    'archivo' => $path.'/'.$item->archivo,
                    'url' => $item->url,
                ];
            })->values();

            $response = parent::successFunc('Consulta exitosa');
            $response['data'] = $data;

            return $this->renderObject($response);
        } catch (DebugException $e) {
            return $this->renderObject(parent::errorFunc($e->getMessage()));
        }
    }

    public function guardar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            DB::beginTransaction();

            $numedu = (Mercurio73::max('numedu') ?? 0) + 1;
            $orden = (Mercurio73::max('orden') ?? 0) + 1;
            $url = $request->input('url');

            $mercurio73 = new Mercurio73;
            $mercurio73->setNumedu($numedu);
            $mercurio73->setOrden($orden);
            $mercurio73->setUrl($url);
            $mercurio73->setEstado('A');

            $mercurio01 = Mercurio01::first();
            if (! $mercurio01) {
                throw new DebugException('Configuración básica no encontrada.');
            }

            if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
                $file = $request->file('archivo');
                $extension = $file->getClientOriginalExtension();
                $fileName = 'promo_educacion_'.$numedu.'.'.$extension;
                $destinationPath = public_path($mercurio01->getPath().'galeria');
                $file->move($destinationPath, $fileName);
                $mercurio73->setArchivo($fileName);
            } else {
                throw new DebugException('No se ha subido ningún archivo o el archivo no es válido.');
            }

            if (! $mercurio73->save()) {
                parent::setLogger($mercurio73->getMessages());
                DB::rollBack();
                throw new DebugException('Error al guardar la promoción.');
            }

            DB::commit();
            $response = parent::successFunc('Creacion terminada Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            DB::rollBack();
            $response = parent::errorFunc('No se puede guardar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function arriba(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');

            DB::beginTransaction();
            $objetivo = Mercurio73::where('numedu', $numpro)->first();
            if (! $objetivo) {
                throw new DebugException('Registro no encontrado.');
            }

            $orden_obj = $objetivo->getOrden();
            $minimo = Mercurio73::min('orden');

            if ($orden_obj != $minimo) {
                $superior = Mercurio73::where('orden', '<', $orden_obj)->orderBy('orden', 'desc')->first();
                if ($superior) {
                    $orden_sup = $superior->getOrden();
                    $objetivo->orden = $orden_sup;
                    $superior->orden = $orden_obj;
                    $objetivo->save();
                    $superior->save();
                }
            }
            DB::commit();
            $response = parent::successFunc('Ordenado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            DB::rollBack();
            $response = parent::errorFunc('No se puede Ordenar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function abajo(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');

            DB::beginTransaction();
            $objetivo = Mercurio73::where('numedu', $numpro)->first();
            if (! $objetivo) {
                throw new DebugException('Registro no encontrado.');
            }

            $orden_obj = $objetivo->getOrden();
            $maximo = Mercurio73::max('orden');

            if ($orden_obj != $maximo) {
                $inferior = Mercurio73::where('orden', '>', $orden_obj)->orderBy('orden', 'asc')->first();
                if ($inferior) {
                    $orden_inf = $inferior->getOrden();
                    $objetivo->orden = $orden_inf;
                    $inferior->orden = $orden_obj;
                    $objetivo->save();
                    $inferior->save();
                }
            }
            DB::commit();
            $response = parent::successFunc('Ordenado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            DB::rollBack();
            $response = parent::errorFunc('No se puede Ordenar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function borrar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numpro = $request->input('numpro');

            DB::beginTransaction();
            $mercurio73 = Mercurio73::where('numedu', $numpro)->first();

            if ($mercurio73) {
                $archivo = $mercurio73->getArchivo();
                $mercurio01 = Mercurio01::first();

                if ($mercurio01 && ! empty($archivo)) {
                    $filePath = public_path($mercurio01->getPath().'galeria/'.$archivo);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $mercurio73->delete();
            } else {
                throw new DebugException('El registro a borrar no existe.');
            }

            DB::commit();
            $response = parent::successFunc('Borrado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            DB::rollBack();
            $response = parent::errorFunc('No se puede Borrar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }
}
