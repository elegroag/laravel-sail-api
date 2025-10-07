<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio58;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class Mercurio58Controller extends ApplicationController
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

    public function indexAction($codare)
    {
        return view('cajas.mercurio58.index', [
            'title' => 'Archivos Areas',
            'help' => 'Esta opcion permite manejar los archivos de las áreas.',
            'codare' => $codare
        ]);
    }

    public function galeriaAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $codare = $request->input("codare");
            $mercurio01 = Mercurio01::first();
            if (!$mercurio01) {
                throw new DebugException("Configuración básica no encontrada.");
            }

            $path = url($mercurio01->getPath() . 'galeria');
            $galeria = Mercurio58::where('codare', $codare)->orderBy('orden', 'ASC')->get();

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
            $codare = $request->input("codare");
            $this->db->begin();

            $numero = (Mercurio58::max('numero') ?? 0) + 1;
            $orden = (Mercurio58::max('orden') ?? 0) + 1;

            $mercurio58 = new Mercurio58();
            $mercurio58->setNumero($numero);
            $mercurio58->setCodare($codare);
            $mercurio58->setOrden($orden);

            $mercurio01 = Mercurio01::first();
            if (!$mercurio01) {
                throw new DebugException("Configuración básica no encontrada.");
            }

            if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
                $file = $request->file('archivo');
                $extension = $file->getClientOriginalExtension();
                $fileName = "area_{$codare}_" . $numero . "." . $extension;
                $destinationPath = public_path($mercurio01->getPath() . 'galeria');
                $file->move($destinationPath, $fileName);
                $mercurio58->setArchivo($fileName);
            } else {
                throw new DebugException("No se ha subido ningún archivo o el archivo no es válido.");
            }

            if (!$mercurio58->save()) {
                parent::setLogger($mercurio58->getMessages());
                $this->db->rollback();
                throw new DebugException("Error al guardar el archivo del área.");
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
            $objetivo = Mercurio58::where('numero', $numero)->first();
            if (!$objetivo) {
                throw new DebugException("Registro no encontrado.");
            }

            $orden_obj = $objetivo->getOrden();
            $minimo = Mercurio58::where('codare', $objetivo->codare)->min('orden');

            if ($orden_obj != $minimo) {
                $superior = Mercurio58::where('codare', $objetivo->codare)
                    ->where('orden', '<', $orden_obj)
                    ->orderBy('orden', 'desc')->first();
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
            $objetivo = Mercurio58::where('numero', $numero)->first();
            if (!$objetivo) {
                throw new DebugException("Registro no encontrado.");
            }

            $orden_obj = $objetivo->getOrden();
            $maximo =  Mercurio58::where('codare', $objetivo->codare)->max('orden');

            if ($orden_obj != $maximo) {
                $inferior = Mercurio58::where('codare', $objetivo->codare)
                    ->where('orden', '>', $orden_obj)
                    ->orderBy('orden', 'asc')->first();
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
            $mercurio58 = Mercurio58::where('numero', $numero)->first();

            if ($mercurio58) {
                $archivo = $mercurio58->getArchivo();
                $mercurio01 = Mercurio01::first();

                if ($mercurio01 && !empty($archivo)) {
                    $filePath = public_path($mercurio01->getPath() . 'galeria/' . $archivo);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $mercurio58->delete();
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
