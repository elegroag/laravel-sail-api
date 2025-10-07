<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio53;
use Illuminate\Http\Request;

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
        return view('cajas.mercurio53.index', [
            'title' => 'Destacadas',
            'help' => 'Esta opcion permite manejar las imagenes destacadas.'
        ]);
    }

    public function galeriaAction()
    {
        try {
            $this->setResponse("ajax");
            $mercurio01 = Mercurio01::first();
            if (!$mercurio01) {
                throw new DebugException("Configuración básica no encontrada.");
            }

            $path = url($mercurio01->getPath() . 'galeria');
            $galeria = Mercurio53::orderBy('orden', 'ASC')->get();

            $response = $galeria->map(function ($item) use ($path) {
                return [
                    'numero' => $item->numero,
                    'archivo' => $path . '/' . $item->archivo,
                ];
            });

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc($e->getMessage());
            return $this->renderObject($response, false);
        }
    }

    public function guardarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $this->db->begin();

            $numero = (Mercurio53::max('numero') ?? 0) + 1;
            $orden = (Mercurio53::max('orden') ?? 0) + 1;

            $mercurio53 = new Mercurio53();
            $mercurio53->setNumero($numero);
            $mercurio53->setOrden($orden);

            $mercurio01 = Mercurio01::first();
            if (!$mercurio01) {
                throw new DebugException("Configuración básica no encontrada.");
            }

            if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
                $file = $request->file('archivo');
                $extension = $file->getClientOriginalExtension();
                $fileName = "promom_" . $numero . "." . $extension;
                $destinationPath = public_path($mercurio01->getPath() . 'galeria');
                $file->move($destinationPath, $fileName);
                $mercurio53->setArchivo($fileName);
            } else {
                throw new DebugException("No se ha subido ningún archivo o el archivo no es válido.");
            }

            if (!$mercurio53->save()) {
                parent::setLogger($mercurio53->getMessages());
                $this->db->rollback();
                throw new DebugException("Error al guardar la imagen destacada.");
            }

            $this->db->commit();
            $response = parent::successFunc("Creacion terminada Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc("No se puede guardar el Registro: " . $e->getMessage());
            return $this->renderObject($response, false);
        }
    }

    public function arribaAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $numero = $request->input('numero');

            $this->db->begin();
            $objetivo = Mercurio53::where('numero', $numero)->first();
            if (!$objetivo) {
                throw new DebugException("Registro no encontrado.");
            }

            $orden_obj = $objetivo->getOrden();
            $minimo = Mercurio53::min('orden');

            if ($orden_obj != $minimo) {
                $superior = Mercurio53::where('orden', '<', $orden_obj)->orderBy('orden', 'desc')->first();
                if ($superior) {
                    $orden_sup = $superior->getOrden();
                    $objetivo->orden = $orden_sup;
                    $superior->orden = $orden_obj;
                    $objetivo->save();
                    $superior->save();
                }
            }
            $this->db->commit();
            $response = parent::successFunc("Ordenado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc("No se puede Ordenar el Registro: " . $e->getMessage());
            return $this->renderObject($response, false);
        }
    }

    public function abajoAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $numero = $request->input('numero');

            $this->db->begin();
            $objetivo = Mercurio53::where('numero', $numero)->first();
            if (!$objetivo) {
                throw new DebugException("Registro no encontrado.");
            }

            $orden_obj = $objetivo->getOrden();
            $maximo =  Mercurio53::max('orden');

            if ($orden_obj != $maximo) {
                $inferior = Mercurio53::where('orden', '>', $orden_obj)->orderBy('orden', 'asc')->first();
                if ($inferior) {
                    $orden_inf = $inferior->getOrden();
                    $objetivo->orden = $orden_inf;
                    $inferior->orden = $orden_obj;
                    $objetivo->save();
                    $inferior->save();
                }
            }
            $this->db->commit();
            $response = parent::successFunc("Ordenado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc("No se puede Ordenar el Registro: " . $e->getMessage());
            return $this->renderObject($response, false);
        }
    }

    public function borrarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $numero = $request->input('numero');

            $this->db->begin();
            $mercurio53 = Mercurio53::where('numero', $numero)->first();

            if ($mercurio53) {
                $archivo = $mercurio53->getArchivo();
                $mercurio01 = Mercurio01::first();

                if ($mercurio01 && !empty($archivo)) {
                    $filePath = public_path($mercurio01->getPath() . 'galeria/' . $archivo);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $mercurio53->delete();
            } else {
                throw new DebugException("El registro a borrar no existe.");
            }

            $this->db->commit();
            $response = parent::successFunc("Borrado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc("No se puede Borrar el Registro: " . $e->getMessage());
            return $this->renderObject($response, false);
        }
    }
}
